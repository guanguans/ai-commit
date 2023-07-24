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
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\TableSeparator;
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
    protected $description = 'Automagically generate conventional commit messages with AI.';

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
        $this->task('1. Generating commit messages', function () use (&$messages): void {
            // Ensure git is installed and the current directory is a git repository.
            $this->createProcess(['git', 'rev-parse', '--is-inside-work-tree'])->mustRun();

            $cachedDiff = $this->createProcess($this->getDiffCommand())->mustRun()->getOutput();
            if ('' === $cachedDiff) {
                throw new TaskException('There are no cached files to commit. Try running `git add` to cache some files.');
            }

            $this->newLine();
            $messages = retry(
                $this->option('retry-times'),
                function ($attempts) use ($cachedDiff): string {
                    if ($attempts > 1) {
                        $this->output->info('retrying...');
                    }

                    $originalMessages = $this->generatorManager
                        ->driver($this->option('generator'))
                        ->generate($this->getPrompt($cachedDiff));
                    $messages = $this->tryFixMessages($originalMessages);
                    if (! str($messages)->isJson()) {
                        throw new TaskException(sprintf(
                            'The generated commit messages(%s) is an invalid JSON.',
                            var_export($originalMessages, true)
                        ));
                    }

                    return $messages;
                },
                $this->option('retry-sleep'),
                $this->configManager->get('retry.when')
            );
            $this->newLine();
        }, 'generating...');

        $this->task('2. Choosing commit message', function () use (&$message, $messages): void {
            $message = collect(json_decode($messages, true, 512, JSON_THROW_ON_ERROR))
                ->tap(function (Collection $messages): void {
                    $this->newLine(2);
                    $this->table(
                        array_keys($messages->first()),
                        $messages->chunk(1)
                            ->transform(static function (Collection $messages): Collection {
                                return $messages->prepend(new TableSeparator());
                            })
                            ->flatten(1)
                            ->skip(1)
                    );
                })
                ->pipe(function (Collection $messages) {
                    $subject = $this->choice(
                        'Please choice a commit message',
                        $messages->pluck('subject', 'id')->add($regeneratePhrase = '<comment>regenerate</comment>')->all(),
                        '1'
                    );

                    if ($subject === $regeneratePhrase) {
                        $this->output->note('regenerating...');
                        $this->handle();
                    }

                    return $messages->firstWhere('subject', $subject) ?? [];
                });
        }, 'choosing...');

        $this->task('3. Committing message', function () use ($message): void {
            tap($this->createProcess($this->getCommitCommand($message)), function (Process $process): void {
                $this->isEditMode() and $process->setTty(true)->setTimeout(null);
            })->mustRun();
        }, 'committing...');

        $this->output->success('Successfully generated and committed messages.');

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
            new InputOption('commit-options', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git commit` command', $this->configManager->get('commit_options', [])),
            new InputOption('diff-options', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git diff` command', $this->configManager->get('diff_options', [])),
            new InputOption('generator', 'g', InputOption::VALUE_REQUIRED, 'Specify generator name', $this->configManager->get('generator')),
            new InputOption('prompt', 'p', InputOption::VALUE_REQUIRED, 'Specify prompt name of messages generated', $this->configManager->get('prompt')),
            new InputOption('no-edit', null, InputOption::VALUE_NONE, 'Force no edit mode'),
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Specify config file'),
            new InputOption('retry-times', null, InputOption::VALUE_REQUIRED, 'Specify times of retry', $this->configManager->get('retry.times', 3)),
            new InputOption('retry-sleep', null, InputOption::VALUE_REQUIRED, 'Specify sleep milliseconds of retry', $this->configManager->get('retry.sleep', 500)),
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
    private function createProcess(array $command, ?string $cwd = null, ?array $env = null, $input = null, ?float $timeout = 60): Process
    {
        if (null === $cwd) {
            $cwd = $this->argument('path');
        }

        return tap(new Process($command, $cwd, $env, $input, $timeout), function (Process $process): void {
            if ($this->option('verbose')) {
                $this->output->info($process->getCommandLine());
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
                $this->output->info((string) $prompt);

                return $prompt;
            });
    }

    private function tryFixMessages(string $messages): string
    {
        return (new JsonFixer())
            // ->missingValue('')
            ->silent()
            ->fix(substr($messages, (int) strpos($messages, '[')));
    }

    /**
     * @psalm-suppress RedundantCondition
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function getCommitCommand(array $message): array
    {
        $options = collect($this->option('commit-options'))
            ->push('--edit')
            ->when($this->isNotEditMode(), static function (Collection $collection): Collection {
                return $collection->filter(static function (string $option): bool {
                    return '--edit' !== $option && '-e' !== $option;
                });
            })
            ->all();

        $message = collect($message)
            ->filter(static function ($val): bool {
                return $val && ! is_numeric($val);
            })
            ->map(static function (string $val): string {
                return trim($val, " \t\n\r\x0B");
            })
            ->implode(str_repeat(PHP_EOL, 2));

        return array_merge(['git', 'commit', '--message', $message], $options);
    }

    private function isEditMode(): bool
    {
        return ! windows_os() && ! $this->option('no-edit') && $this->configManager->get('edit');
    }

    private function isNotEditMode(): bool
    {
        return ! $this->isEditMode();
    }
}
