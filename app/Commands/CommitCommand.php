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
use Symfony\Component\Process\Process;

class CommitCommand extends Command
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
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Specify config file'),
            new InputOption('generator', 'g', InputOption::VALUE_REQUIRED, 'Specify generator name', $this->configManager->get('generator')),
            new InputOption('num', null, InputOption::VALUE_REQUIRED, 'Specify number of generated messages', $this->configManager->get('num')),
            new InputOption('prompt', 'p', InputOption::VALUE_REQUIRED, 'Specify prompt name of messages generated', $this->configManager->get('prompt')),
            new InputOption('no-edit', null, InputOption::VALUE_NONE, 'Force no edit mode'),
        ]);
    }

    /**
     * @psalm-suppress InvalidScalarArgument
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $config = $this->option('config') and $this->configManager->replaceFrom($config);
    }

    public function handle(GeneratorManager $generatorManager): int
    {
        $this->task('1. Generating commit messages', function () use ($generatorManager, &$messages): void {
            $isInsideWorkTree = $this->createProcess('git rev-parse --is-inside-work-tree')
                ->mustRun()
                ->getOutput();
            if (! \str($isInsideWorkTree)->rtrim()->is('true')) {
                throw new TaskException(<<<'message'
It looks like you are not in a git repository.
Please run this command from the root of a git repository, or initialize one using `git init`.
message);
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
            $messages = collect(json_decode($messages, true));
            $chosenSubject = $this->choice('Please choice a commit message', $messages->pluck('subject', 'id')->all());
            $message = $messages->first(static function ($message) use ($chosenSubject): bool {
                return $message['subject'] === $chosenSubject;
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
        return (string) \str($this->configManager->get("prompts.{$this->option('prompt')}"))
            ->replace(
                [$this->configManager->get('diff_mark'), $this->configManager->get('num_mark')],
                [$stagedDiff, $this->option('num')]
            )
            ->when($this->option('verbose'), function (Stringable $diff): void {
                $this->output->info($diff->__toString());
            });
    }

    protected function tryFixMessages(string $messages): string
    {
        return (string) (new JsonFixer())
            ->missingValue('')
            ->silent()
            ->fix(substr($messages, strpos($messages, '[')));
    }

    protected function getCommitCommand(array $message): array
    {
        return collect($message)
            ->filter(static function ($val): bool {
                return $val && is_string($val);
            })
            ->map(static function (string $val): string {
                return trim($val, " \t\n\r\x0B");
            })
            ->pipe(function (Collection $message): array {
                $options = collect($this->option('commit-options'))
                    ->push('--edit')
                    ->pipe(function (Collection $options): Collection {
                        $noEdit = $this->option('no-edit') ?: ! $this->configManager->get('edit');
                        if ($noEdit) {
                            return $options->filter(static function (string $option): bool {
                                return '--edit' !== $option;
                            });
                        }

                        return $options;
                    })
                    ->all();

                return array_merge(['git', 'commit', '--message', $message->implode(str_repeat(PHP_EOL, 2))], $options);
            });
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
