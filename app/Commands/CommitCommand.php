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

use App\Contracts\GeneratorContract;
use App\Contracts\OutputAwareContract;
use App\Exceptions\TaskException;
use App\GeneratorManager;
use Composer\Console\Input\InputOption;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CommitCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = /** @lang text */
        '
        commit
        // {--commit-options=* : Append options for the `git commit` command <comment>[default: "--edit"]</comment>
        // {--diff-options=* : Append options for the `git diff` command <comment>[default: ":!*.lock"]</comment>
        // {--generator=openai : Specify generator
        // {--num=3 : Specify number of generated messages
        // {--prompt= : Specify prompt name of messages generated
    ';

    protected $description = 'Automagically generate commit messages with AI.';

    /**
     * The configuration of the command.
     *
     * @return void
     */
    protected function configure()
    {
        /** @var \App\ConfigManager $config */
        $config = resolve(Repository::class)->get('ai-commit');

        $this->setDefinition([
            new InputOption('commit-options', '', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git commit` command', $config->get('commit_options')),
            new InputOption('diff-options', '', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Append options for the `git diff` command', $config->get('diff_options')),
            new InputOption('generator', '', InputOption::VALUE_REQUIRED, 'Specify generator name', $config->get('generator')),
            new InputOption('num', '', InputOption::VALUE_REQUIRED, 'Specify number of generated messages', $config->get('num')),
            new InputOption('prompt', '', InputOption::VALUE_REQUIRED, 'Specify prompt name of messages generated', $config->get('prompt')),
        ]);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    public function handle()
    {
        $this->task('1. Checking environment', function () use (&$stagedDiff) {
            $isInsideWorkTree = Process::fromShellCommandline('git rev-parse --is-inside-work-tree')
                ->mustRun()
                ->getOutput();
            if (! \str($isInsideWorkTree)->rtrim()->is('true')) {
                $message = <<<'message'
It looks like you are not in a git repository.
Please run this command from the root of a git repository, or initialize one using `git init`.
message;

                throw new TaskException($message);
            }

            $stagedDiff = (new Process($this->getDiffCommand()))->mustRun()->getOutput();
            if (empty($stagedDiff)) {
                throw new TaskException('There are no staged files to commit. Try running `git add` to stage some files.');
            }
        }, 'checking...');

        $this->task('2. Generating commit messages', function () use (&$commitMessages, $stagedDiff) {
            $commitMessages = $this->getGenerator()->generate($this->getPromptOfAI($stagedDiff));
            if (\str($commitMessages)->isEmpty()) {
                throw new TaskException('No commit messages generated.');
            }

            if (! is_json($commitMessages)) {
                throw new TaskException('The generated commit messages is an invalid JSON.');
            }

            $this->line('');
            $this->line('');
        }, 'generating...');

        $this->task('3. Choosing commit message', function () use ($commitMessages, &$commitMessage) {
            $commitMessages = collect(json_decode($commitMessages, true));

            $chosenSubject = $this->choice('Please choice a commit message', $commitMessages->pluck('subject', 'id')->all());

            $commitMessage = $commitMessages->first(function ($commitMessage) use ($chosenSubject) {
                return $commitMessage['subject'] === $chosenSubject;
            });
        }, 'choosing...');

        $this->task('4. Committing message', function () use ($commitMessage) {
            (new Process($this->getCommitCommand($commitMessage)))
                ->setTty(true)
                ->setTimeout(null)
                ->mustRun();
        }, 'committing...');

        return self::SUCCESS;
    }

    protected function getDiffCommand(): array
    {
        return array_merge(['git', 'diff', '--staged'], $this->option('diff-options'));
    }

    protected function getGenerator(): GeneratorContract
    {
        $generator = $this->laravel->get(GeneratorManager::class)->driver($this->option('generator'));

        return tap($generator, function (GeneratorContract $generator) {
            $generator instanceof OutputAwareContract and $generator->setOutput($this->output);
        });
    }

    protected function getCommitCommand(array $commitMessage): array
    {
        return collect($commitMessage)
            ->filter(function ($val) {
                return $val && is_string($val);
            })
            ->map(function (string $val) {
                return trim($val, " \t\n\r\x0B");
            })
            ->pipe(function (Collection $collection): array {
                return array_merge(
                    ['git', 'commit', '--message', $collection->implode(str_repeat(PHP_EOL, 2))],
                    $this->option('commit-options')
                );
            });
    }

    protected function getPromptOfAI(string $stagedDiff): string
    {
        /** @var \App\ConfigManager $config */
        $config = $this->laravel->get('config')->get('ai-commit');

        return \str($config->get("prompts.{$this->option('prompt')}"))
            ->replace(
                [$config->get('diff_mark'), $config->get('num_mark')],
                [$stagedDiff, $this->option('num')]
            )
            ->when($this->option('verbose'), function (Stringable $diff) {
                $this->line('');
                $this->line('===================================================');
                $this->line($diff);
                $this->output->write('===================================================');
            })
            ->__toString();
    }

    /**
     * Define the command's schedule.
     *
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
