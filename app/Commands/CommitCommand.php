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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
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

    public function __construct()
    {
        $this->configManager = config('ai-commit');
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
                $this->input->setOption(\str($name)->slug()->__toString(), $value);
            }
        }
    }

    public function handle(GeneratorManager $generatorManager): int
    {
        $this->task('1. Generating commit messages', function () use ($generatorManager, &$messages): void {
            try {
                $process = $this->createProcess('git rev-parse --is-inside-work-tree');
                $process->mustRun();
            } catch (ProcessFailedException $e) {
                $errorOutput = \str($process->getErrorOutput())
                    ->rtrim()
                    ->whenStartsWith('fatal: ', function (Stringable $stringable) use ($process) {
                        return $stringable->append(" [{$process->getWorkingDirectory()}].");
                    })
                    ->__toString();

                throw new TaskException($errorOutput);
            }

            $stagedDiff = $this->createProcess($this->getDiffCommand())->mustRun()->getOutput();
            if (\str($stagedDiff)->isEmpty()) {
                throw new TaskException('There are no staged files to commit. Try running `git add` to stage some files.');
            }

            $messages = $generatorManager->driver($this->option('generator'))->generate($this->getPromptOfAI($stagedDiff));
            if (\str($messages)->isEmpty()) {
                throw new TaskException('No commit messages generated.');
            }

            $messages = $this->tryFixMessages($messages);
            if (! \str($messages)->isJson()) {
                throw new TaskException('The generated commit messages is an invalid JSON.');
            }
        }, 'generating...');

        $this->task('2. Choosing commit message', function () use ($messages, &$message): void {
            $messages = collect(json_decode($messages, true))->when($this->option('verbose'), function (Collection $collection): Collection {
                $this->newLine();
                $collection->dump();

                return $collection;
            });

            $subject = $this->choice('Please choice a commit message', $messages->pluck('subject', 'id')->all(), '1');

            $message = $messages->first(static function ($message) use ($subject): bool {
                return $message['subject'] === $subject;
            });
        }, 'choosing...');

        $this->task('3. Committing message', function () use ($message): void {
            $this->createProcess($this->getCommitCommand($message))
                ->setTty(true)
                ->setTimeout(null)
                ->mustRun();
        }, 'committing...');

        return self::SUCCESS;
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

    protected function getPromptOfAI(string $stagedDiff): string
    {
        return \str($this->configManager->get("prompts.{$this->option('prompt')}"))
            ->replace(
                [$this->configManager->get('diff_mark'), $this->configManager->get('num_mark')],
                [$stagedDiff, $this->option('num')]
            )
            ->when($this->option('verbose'), function (Stringable $diff): void {
                $this->output->info($diff->__toString());
            })
            ->__toString();
    }

    protected function tryFixMessages(string $messages): string
    {
        return (string) (new JsonFixer())
            ->missingValue('')
            ->silent()
            ->fix(substr($messages, strpos($messages, '[')));
    }

    /**
     * @psalm-suppress RedundantCondition
     */
    protected function getCommitCommand(array $message): array
    {
        $options = collect($this->option('commit-options'))
            ->push('--edit')
            ->when($this->option('no-edit') ?: ! $this->configManager->get('edit'), static function (Collection $collection): Collection {
                return $collection->filter(static function (string $option): bool {
                    return '--edit' !== $option || '-e' !== $option;
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

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
