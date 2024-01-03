<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Commands;

use App\ConfigManager;
use App\Exceptions\TaskException;
use App\GeneratorManager;
use App\Support\JsonFixer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class CommitCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'commit';

    /**
     * @var string
     */
    protected $description = 'Automagically generate conventional commit message with AI.';

    /**
     * @var \App\ConfigManager
     */
    private $configManager;

    /**
     * @var \App\GeneratorManager
     */
    private $generatorManager;

    public function __construct(GeneratorManager $generatorManager)
    {
        $this->configManager = config('ai-commit');
        $this->generatorManager = $generatorManager;
        parent::__construct();
    }

    /**
     * @noinspection DebugFunctionUsageInspection
     *
     * @psalm-suppress InvalidArgument
     */
    public function handle(): int
    {
        $this->task('1. Generating commit message', function () use (&$message): void {
            // Ensure git is installed and the current directory is a git repository.
            $this->createProcess(['git', 'rev-parse', '--is-inside-work-tree'])->mustRun();

            $cachedDiff = $this->createProcess($this->getDiffCommand())->mustRun()->getOutput();
            if ('' === $cachedDiff) {
                throw new TaskException('There are no cached files to commit. Try running `git add` to cache some files.');
            }

            $message = retry(
                $this->option('retry-times'),
                function ($attempts) use ($cachedDiff): string {
                    if ($attempts > 1) {
                        $this->output->note('retrying...');
                    }

                    $originalMessage = $this->generatorManager
                        ->driver($this->option('generator'))
                        ->generate($this->getPrompt($cachedDiff));
                    $message = $this->tryFixMessage($originalMessage);
                    if (! str($message)->isJson()) {
                        throw new TaskException(sprintf(
                            'The generated commit message(%s) is an invalid JSON.',
                            var_export($originalMessage, true)
                        ));
                    }

                    return $message;
                },
                $this->option('retry-sleep'),
                $this->configManager->get('retry.when')
            );
        }, 'generating...'.PHP_EOL);

        $this->task(PHP_EOL.'2. Confirming commit message', function () use (&$message): void {
            $message = collect(json_decode($message, true, 512, JSON_THROW_ON_ERROR))
                ->pipe(static function (Collection $message): Collection {
                    if (\is_array($message['body'])) {
                        $message['body'] = collect($message['body'])
                            ->transform(static function (string $line): string {
                                return Str::start(trim($line, " \t\n\r\x0B"), '- ');
                            })
                            ->implode(PHP_EOL);
                    }

                    return $message;
                })
                ->tap(function (Collection $message): void {
                    $this->table(
                        $message->keys()->all(),
                        [$message->all()]
                    );
                })
                ->tap(function (): void {
                    if (! $this->confirm('Do you want to commit this message?', true)) {
                        $this->output->note('regenerating...');
                        $this->handle();
                    }
                });
        }, 'confirming...'.PHP_EOL);

        $this->task(PHP_EOL.'3. Committing message', function () use ($message): void {
            tap($this->createProcess($this->getCommitCommand($message)), function (Process $process): void {
                $this->shouldEdit() and $process->setTty(true);
            })->setTimeout(null)->mustRun();
        }, 'committing...'.PHP_EOL);

        $this->output->success('Successfully generated and committed message.');

        return self::SUCCESS;
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress InvalidArgument
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('generator')) {
            $suggestions->suggestValues(array_keys($this->configManager->get('generators', [])));

            return;
        }

        if ($input->mustSuggestOptionValuesFor('prompt')) {
            $suggestions->suggestValues(array_keys($this->configManager->get('prompts', [])));
        }
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::OPTIONAL, 'The working directory', ConfigManager::localPath('')),
            new InputOption(
                'commit-options',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Append options for the `git commit` command',
                $this->configManager->get('commit_options', [])
            ),
            new InputOption(
                'diff-options',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Append options for the `git diff` command',
                $this->configManager->get('diff_options', [])
            ),
            new InputOption(
                'generator',
                'g',
                InputOption::VALUE_REQUIRED,
                'Specify generator name',
                $this->configManager->get('generator')
            ),
            new InputOption(
                'prompt',
                'p',
                InputOption::VALUE_REQUIRED,
                'Specify prompt name of message generated',
                $this->configManager->get('prompt')
            ),
            new InputOption(
                'no-edit',
                null,
                InputOption::VALUE_NONE,
                'Enable or disable git commit `--no-edit` option'
            ),
            new InputOption(
                'no-verify',
                null,
                InputOption::VALUE_NONE,
                'Enable or disable git commit `--no-verify` option'
            ),
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Specify config file'),
            new InputOption(
                'retry-times',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify times of retry',
                $this->configManager->get('retry.times', 3)
            ),
            new InputOption(
                'retry-sleep',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify sleep milliseconds of retry',
                $this->configManager->get('retry.sleep', 500)
            ),
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \JsonException
     *
     * @psalm-suppress InvalidScalarArgument
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        if ($configFile = $this->option('config')) {
            $this->configManager->replaceFrom($configFile);

            $options = $this->configManager->getMany([
                'commit_options',
                'diff_options',
                'generator',
                'prompt',
                'retry.times',
                'retry.sleep',
            ]);

            collect($options)
                ->reject(static function ($value): bool {
                    return null === $value;
                })
                ->each(function ($value, $name): void {
                    $this->input->setOption((string) str($name)->replace(['.', '_'], '-'), $value);
                });
        }
    }

    /**
     * @param null|mixed $input
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function createProcess(
        array $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        ?float $timeout = 60
    ): Process {
        if (null === $cwd) {
            $cwd = $this->argument('path');
        }

        return tap(new Process($command, $cwd, $env, $input, $timeout), function (Process $process): void {
            if ($this->option('verbose')) {
                $this->output->note($process->getCommandLine());
            }
        });
    }

    private function getDiffCommand(): array
    {
        return array_merge(['git', 'diff', '--cached'], $this->option('diff-options'));
    }

    private function getPrompt(string $cachedDiff): string
    {
        return (string) str($this->configManager->get("prompts.{$this->option('prompt')}"))
            ->replace($this->configManager->get('diff_mark'), $cachedDiff)
            ->when($this->option('verbose'), function (Stringable $prompt): Stringable {
                $this->output->note((string) $prompt);

                return $prompt;
            });
    }

    private function tryFixMessage(string $message): string
    {
        return (new JsonFixer())
            // ->missingValue('')
            ->silent()
            ->fix(substr($message, (int) strpos($message, '[')));
    }

    /**
     * @psalm-suppress RedundantCondition
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function getCommitCommand(Collection $message): array
    {
        $options = collect($this->option('commit-options'))
            ->when($this->shouldntEdit(), static function (Collection $collection): Collection {
                return $collection->add('--no-edit');
            })
            ->when($this->shouldntVerify(), static function (Collection $collection): Collection {
                return $collection->add('--no-verify');
            })
            ->all();

        $message = $message
            ->map(static function (string $val): string {
                return trim($val, " \t\n\r\x0B");
            })
            ->filter(static function ($val) {
                return $val;
            })
            ->implode(str_repeat(PHP_EOL, 2));

        return array_merge(['git', 'commit', '--message', $message], $options);
    }

    private function shouldntEdit(): bool
    {
        return ! Process::isTtySupported() || $this->option('no-edit') || $this->configManager->get('no_edit');
    }

    private function shouldEdit(): bool
    {
        return ! $this->shouldntEdit();
    }

    private function shouldntVerify(): bool
    {
        return $this->option('no-verify') || $this->configManager->get('no_verify');
    }

    /**
     * @codeCoverageIgnore
     */
    private function shouldVerify(): bool
    {
        return ! $this->shouldntVerify();
    }
}
