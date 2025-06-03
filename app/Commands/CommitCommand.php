<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Commands;

use App\ConfigManager;
use App\Exceptions\RuntimeException;
use App\GeneratorManager;
use Illuminate\Support\Collection;
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
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $signature = 'commit';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $description = 'Automagically generate conventional commit message with AI.';
    private readonly ConfigManager $configManager;

    public function __construct(private readonly GeneratorManager $generatorManager)
    {
        $this->configManager = config('ai-commit');
        parent::__construct();
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function handle(): void
    {
        collect()
            ->tap(function (): void {
                $this->createProcess(['git', 'rev-parse', '--is-inside-work-tree'])->mustRun();
            })
            ->tap(function () use (&$cachedDiff): void {
                $cachedDiff = $this->option('diff') ?: $this->createProcess($this->diffCommand())->mustRun()->getOutput();

                if (empty($cachedDiff)) {
                    throw new RuntimeException('There are no cached files to commit. Try running `git add` to cache some files.');
                }
            })
            ->tap(function () use (&$type): void {
                $type = $this->choice(
                    'Please choice commit type',
                    $types = $this->configManager->get('types'),
                    array_key_first($types)
                );
            })
            ->tap(function () use (&$message, $cachedDiff, $type): void {
                $message = $this->sanitizeMessage(
                    $this->generatorManager
                        ->driver($this->option('generator'))
                        ->generate($this->promptFor($cachedDiff, $type))
                );
            })
            ->tap(function () use (&$message): void {
                $len = str($message)->explode(\PHP_EOL)->map(static fn (string $line): int => mb_strlen($line))->max();
                $this->newLine();
                $this->line(str_repeat('-', $len));
                $this->info($message);
                $this->line(str_repeat('-', $len));

                if (!$this->confirm('Do you want to commit this message?', true)) {
                    $this->output->note('regenerating...');
                    $this->handle();
                }
            })
            ->tap(function () use ($message): void {
                if ($this->option('dry-run')) {
                    $this->info($message);

                    return;
                }

                tap($this->createProcess($this->commitCommandFor($message)), function (Process $process): void {
                    $this->shouldEdit() and $process->setTty(true);
                })->setTimeout(null)->mustRun();
            })
            ->tap(function (): void {
                $this->output->success('Successfully generated and committed message.');
            });
    }

    /**
     * @codeCoverageIgnore
     *
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MissingParentCallInspection
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
     * {@inheritDoc}
     *
     * @noinspection MethodVisibilityInspection
     * @noinspection PhpMissingParentCallCommonInspection
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
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Only generate message without commit',
            ),
            new InputOption(
                'diff',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify diff content',
            ),
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection MethodVisibilityInspection
     * @noinspection PhpMissingParentCallCommonInspection
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
            ]);

            collect($options)
                ->reject(static fn (mixed $value): bool => null === $value)
                ->each(function (mixed $value, string $name): void {
                    $this->input->setOption((string) str($name)->replace(['.', '_'], '-'), $value);
                });
        }
    }

    private function diffCommand(): array
    {
        return ['git', 'diff', '--cached', ...$this->option('diff-options')];
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function commitCommandFor(string $message): array
    {
        $options = collect($this->option('commit-options'))
            ->when($this->shouldntEdit(), static fn (Collection $collection): Collection => $collection->add('--no-edit'))
            ->when($this->shouldntVerify(), static fn (Collection $collection): Collection => $collection->add('--no-verify'))
            ->all();

        return ['git', 'commit', '--message', $message, ...$options];
    }

    private function promptFor(string $cachedDiff, string $type): string
    {
        $typePrompt = \sprintf($this->configManager->get('type_prompt'), $type);

        if (array_key_first($this->configManager->get('types')) === $type) {
            $type = $this->configManager->get('type_mark'); // Reset type.
            $typePrompt = ''; // Clear type prompt.
        }

        return (string) str($this->configManager->get("prompts.{$this->option('prompt')}"))
            ->replace($this->configManager->get('type_mark'), $type)
            ->replace($this->configManager->get('type_prompt_mark'), $typePrompt)
            ->replace($this->configManager->get('diff_mark'), $cachedDiff)
            ->when($this->option('verbose'), function (Stringable $prompt): Stringable {
                $this->output->note((string) $prompt);

                return $prompt;
            });
    }

    /**
     * @todo
     */
    private function sanitizeMessage(string $message): string
    {
        return $message;
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     * @noinspection PhpSameParameterValueInspection
     */
    private function createProcess(
        array $command,
        ?string $cwd = null,
        ?array $env = null,
        mixed $input = null,
        ?float $timeout = 60
    ): Process {
        null === $cwd and $cwd = $this->argument('path');

        return tap(new Process($command, $cwd, $env, $input, $timeout), function (Process $process): void {
            if ($this->output->isDebug()) {
                $this->output->note($process->getCommandLine());
            }
        });
    }

    private function shouldntEdit(): bool
    {
        return !Process::isTtySupported() || $this->option('no-edit') || $this->configManager->get('no_edit');
    }

    private function shouldEdit(): bool
    {
        return !$this->shouldntEdit();
    }

    private function shouldntVerify(): bool
    {
        return $this->option('no-verify') || $this->configManager->get('no_verify');
    }
}
