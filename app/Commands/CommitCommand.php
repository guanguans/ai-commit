<?php

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
use App\Support\JsonFixer;
use Illuminate\Console\Scheduling\Schedule;
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
    protected $signature = 'commit';
    protected $description = 'Automagically generate conventional commit message with AI.';
    private readonly ConfigManager $configManager;

    public function __construct(private readonly GeneratorManager $generatorManager)
    {
        $this->configManager = config('ai-commit');
        parent::__construct();
    }

    /**
     * @throws \Exception
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
            ->tap(function () use ($type, $cachedDiff, &$message): void {
                $message = retry(
                    $this->option('retry-times'),
                    function ($attempts) use ($cachedDiff, $type): string {
                        if (1 < $attempts) {
                            $this->output->note('retrying...');
                        }

                        $originalMessage = $this->generatorManager
                            ->driver($this->option('generator'))
                            ->generate($this->promptFor($cachedDiff, $type));
                        $message = $this->tryFixMessage($originalMessage);

                        if (!str($message)->jsonValidate()) {
                            throw new RuntimeException(\sprintf(
                                'The generated commit message(%s) is an invalid JSON.',
                                var_export($originalMessage, true)
                            ));
                        }

                        return $message;
                    },
                    $this->option('retry-sleep'),
                    $this->configManager->get('retry.when')
                );
            })
            ->tap(function () use (&$message): void {
                $message = Collection::json($message)
                    ->map(static function ($content) {
                        if (\is_array($content)) {
                            return collect($content)
                                ->transform(static fn (string $line): string => (string) str($line)->trim(" \t\n\r\x0B")->start('- '))
                                ->implode(\PHP_EOL);
                        }

                        return $content;
                    })
                    ->tap(function (Collection $message): void {
                        $message = $message->put('', '')->sortKeysUsing(static function (string $a, string $b): int {
                            $rules = ['subject', '', 'body'];

                            return array_search($a, $rules, true) <=> array_search($b, $rules, true);
                        });
                        // $this->table($message->keys()->all(), [$message->all()]);
                        $this->output->horizontalTable($message->keys()->all(), [$message->all()]);
                    })
                    ->tap(function (): void {
                        if (!$this->confirm('Do you want to commit this message?', true)) {
                            $this->output->note('regenerating...');
                            $this->handle();
                        }
                    });
            })
            ->tap(function () use ($message): void {
                if ($this->option('dry-run')) {
                    $this->info($this->hydrateMessage($message));

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

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    /**
     * @codeCoverageIgnore
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
     * @throws \JsonException
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
                ->reject(static fn ($value): bool => null === $value)
                ->each(function ($value, $name): void {
                    $this->input->setOption((string) str($name)->replace(['.', '_'], '-'), $value);
                });
        }
    }

    private function diffCommand(): array
    {
        return array_merge(['git', 'diff', '--cached'], $this->option('diff-options'));
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function commitCommandFor(Collection $message): array
    {
        $options = collect($this->option('commit-options'))
            ->when($this->shouldntEdit(), static fn (Collection $collection): Collection => $collection->add('--no-edit'))
            ->when($this->shouldntVerify(), static fn (Collection $collection): Collection => $collection->add('--no-verify'))
            ->all();

        return array_merge(['git', 'commit', '--message', $this->hydrateMessage($message)], $options);
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
     * @see https://github.com/josdejong/jsonrepair
     * @see https://github.com/adhocore/php-json-fixer
     */
    private function tryFixMessage(string $message): string
    {
        return (new JsonFixer)
            // ->missingValue('')
            ->silent()
            ->fix(
                str($message)
                    // ->substr((int) strpos($message, '{'))
                    ->after($flag = '{')
                    ->start($flag)
                    ->trim()
                    ->remove([
                        // PHP_EOL,
                    ])
                    ->replace(
                        array_keys($replaceRules = [
                            // "\\'" => "'",
                        ]),
                        $replaceRules
                    )
                    ->pipe(static fn (Stringable $message): Stringable => collect([
                        // '/,\s*]/' => ']', // 数组中最后一个元素后的逗号
                        // '/,\s*}/' => '}', // 对象中最后一个属性后的逗号
                        // '/:\s*[\[\{]/' => ':[]', // 对象的属性值如果是数组或对象，确保有正确的格式
                        // '/:\s*null\s*,/' => ':null,', // null 后面不应有逗号
                        // '/:\s*true\s*,/' => ':true,', // true 后面不应有逗号
                        // '/:\s*false\s*,/' => ':false,', // false 后面不应有逗号
                        // '/:\s*"[^"]*"\s*,/' => ':"",', // 字符串后面不应有逗号
                        // '/,\s*,/' => ',', // 连续的逗号
                        // '/[\x00-\x1F\x7F-\x9F]/mu' => '', // 控制字符
                        '/[[:cntrl:]]/mu' => '', // 控制字符
                        '/\s+/' => ' ', // 连续的空格
                        '/\\\\(?!["\\\\\/bfnrt]|u[0-9a-fA-F]{4})/' => '$1', // 非法转义字符
                    ])->reduce(static fn (Stringable $message, string $replace, string $pattern): Stringable => $message->replaceMatches($pattern, $replace), $message))
                    // ->dd()
                    ->jsonSerialize()
            );
    }

    private function hydrateMessage(Collection $message): string
    {
        return $message
            ->map(static fn (string $val): string => trim($val, " \t\n\r\x0B"))
            ->filter(static fn ($val): string => $val)
            ->implode(str_repeat(\PHP_EOL, 2));
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     * @noinspection PhpSameParameterValueInspection
     * @noinspection MissingParameterTypeDeclarationInspection
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

    /**
     * @codeCoverageIgnore
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function shouldVerify(): bool
    {
        return !$this->shouldntVerify();
    }
}
