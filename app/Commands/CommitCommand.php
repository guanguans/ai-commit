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
    protected $configManager;

    /**
     * @var \App\GeneratorManager
     */
    protected $generatorManager;

    public function __construct(GeneratorManager $generatorManager)
    {
        $this->configManager = config('ai-commit');
        $this->generatorManager = $generatorManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::OPTIONAL, 'The working directory', $this->configManager::localPath('')),
            new InputOption('commit-options', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git commit` command', $this->configManager->get('commit_options')),
            new InputOption('diff-options', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git diff` command', $this->configManager->get('diff_options')),
            new InputOption('generator', 'g', InputOption::VALUE_REQUIRED, 'Specify generator name', $this->configManager->get('generator')),
            new InputOption('prompt', 'p', InputOption::VALUE_REQUIRED, 'Specify prompt name of messages generated', $this->configManager->get('prompt')),
            new InputOption('num', null, InputOption::VALUE_REQUIRED, 'Specify number of generated messages', $this->configManager->get('num')),
            new InputOption('no-edit', null, InputOption::VALUE_NONE, 'Force no edit mode'),
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Specify config file'),
        ]);
    }

    /**
     * @psalm-suppress InvalidScalarArgument
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($configFile = $this->option('config')) {
            $this->configManager->replaceFrom($configFile);

            $options = $this->configManager->getMany([
                'commit_options',
                'diff_options',
                'generator',
                'num',
                'prompt',
            ]);

            foreach ($options as $name => $value) {
                null === $value or $this->input->setOption((string) \str($name)->slug(), $value);
            }
        }
    }

    /**
     * @noinspection DebugFunctionUsageInspection
     */
    public function handle(): int
    {
        $this->task('1. Generating commit messages', function () use (&$messages): void {
            $process = tap($this->createProcess('git rev-parse --is-inside-work-tree'))->run();
            if (! $process->isSuccessful()) {
                throw new TaskException(trim($process->getErrorOutput()));
            }

            $stagedDiff = $this->createProcess($this->getDiffCommand())->mustRun()->getOutput();
            if (\str($stagedDiff)->isEmpty()) {
                throw new TaskException('There are no staged files to commit. Try running `git add` to stage some files.');
            }

            $originalMessages = $this->generatorManager->driver($this->option('generator'))->generate($this->getPrompt($stagedDiff));
            $messages = $this->tryFixMessages($originalMessages);
            if (! \str($messages)->isJson()) {
                throw new TaskException(sprintf('The generated commit messages(%s) is an invalid JSON.', var_export($originalMessages, true)));
            }
        }, 'generating...');

        $this->task('2. Choosing commit message', function () use ($messages, &$message): void {
            $message = collect(json_decode($messages, true))
                ->tap(function (Collection $messages) {
                    $this->newLine();
                    $this->table(
                        array_keys($messages->first()),
                        $messages->chunk(1)
                            ->transform(function (Collection $messages) {
                                return $messages->prepend(new TableSeparator());
                            })
                            ->flatten(1)
                            ->skip(1)
                    );
                })
                ->pipe(function (Collection $messages) {
                    $subject = $this->choice('Please choice a commit message', $messages->pluck('subject', 'id')->all(), '1');

                    return $messages->firstWhere('subject', $subject) ?? [];
                });
        }, 'choosing...');

        $this->task('3. Committing message', function () use ($message): void {
            tap($this->createProcess($this->getCommitCommand($message)), function (Process $process) {
                $this->isEditMode() and $process->setTty(true)->setTimeout(null);
            })->mustRun();
        }, 'committing...');

        $this->output->success('Generate and commit messages have succeeded');

        return self::SUCCESS;
    }

    /**
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

    /**
     * @param string|array $command
     */
    protected function createProcess($command, string $cwd = null, array $env = null, $input = null, ?float $timeout = 60): Process
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        null === $cwd and $cwd = $this->argument('path');

        $process = is_string($command)
            ? Process::fromShellCommandline($command, $cwd, $env, $input, $timeout)
            : new Process($command, $cwd, $env, $input, $timeout);

        if ($this->option('verbose')) {
            $this->output->info($process->getCommandLine());
        }

        return $process;
    }

    protected function getDiffCommand(): array
    {
        return array_merge(['git', 'diff', '--staged'], $this->option('diff-options'));
    }

    protected function getPrompt(string $stagedDiff): string
    {
        return (string) \str($this->configManager->get("prompts.{$this->option('prompt')}"))
            ->replace(
                [$this->configManager->get('diff_mark'), $this->configManager->get('num_mark')],
                [$stagedDiff, $this->option('num')]
            )
            ->when($this->option('verbose'), function (Stringable $prompt): Stringable {
                $this->output->info((string) $prompt);

                return $prompt;
            });
    }

    protected function tryFixMessages(string $messages): string
    {
        return (string) (new JsonFixer())
            ->missingValue('')
            ->silent()
            ->fix(substr($messages, (int) strpos($messages, '[')));
    }

    /**
     * @psalm-suppress RedundantCondition
     */
    protected function getCommitCommand(array $message): array
    {
        $options = collect($this->option('commit-options'))
            ->push('--edit')
            ->when($this->isNotEditMode(), static function (Collection $collection): Collection {
                return $collection->filter(static function (string $option): bool {
                    return ! ('--edit' === $option || '-e' === $option);
                });
            })
            ->all();

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
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

    protected function isEditMode(): bool
    {
        return ! $this->isNotEditMode();
    }

    protected function isNotEditMode(): bool
    {
        return (bool) ($this->option('no-edit') ?: ! $this->configManager->get('edit'));
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
