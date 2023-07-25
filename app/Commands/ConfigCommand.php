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
use App\Exceptions\RuntimeException;
use App\Exceptions\UnsupportedConfigActionException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class ConfigCommand extends Command
{
    /**
     * @var string[]
     */
    public const ACTIONS = ['set', 'get', 'unset', 'reset', 'list', 'edit'];

    /**
     * @var string[]
     */
    protected const WINDOWS_EDITORS = ['notepad'];

    /**
     * @var string[]
     */
    protected const UNIX_EDITORS = ['editor', 'vim', 'vi', 'nano', 'pico', 'ed'];

    /**
     * @var string
     */
    protected $signature = 'config';

    /**
     * @var string
     */
    protected $description = 'Manage config options.';

    /**
     * @var \App\ConfigManager
     */
    private $configManager;

    public function __construct()
    {
        $this->configManager = config('ai-commit');
        parent::__construct();
    }

    /**
     * @throws \JsonException
     */
    public function handle(): int
    {
        $file = value(function () {
            if ($file = $this->option('file')) {
                return $file;
            }

            if ($this->option('global')) {
                return ConfigManager::globalPath();
            }

            return ConfigManager::localPath();
        });

        $this->output->info("The config file($file) is being operated.");
        file_exists($file) or $this->configManager->putFile($file);
        $this->configManager->replaceFrom($file);
        $action = $this->argument('action');
        $key = $this->argument('key');

        if (\in_array($action, ['set', 'unset'], true) && null === $key) {
            $this->output->error('Please specify the parameter key.');

            return self::FAILURE;
        }

        switch ($action) {
            case 'set':
                $this->configManager->set($key, $this->argToValue($this->argument('value')));
                $this->configManager->putFile($file);

                break;
            case 'get':
                $value = null === $key ? $this->configManager->toJson() : $this->configManager->get($key);
                $this->line($this->valueToArg($value));

                break;
            case 'unset':
                $this->configManager->forget($key);
                $this->configManager->putFile($file);

                break;
            case 'reset':
                $config = require config_path('ai-commit.php');
                $key ? $this->configManager->set($key, Arr::get($config, $key)) : $this->configManager->replace($config);
                $this->configManager->putFile($file);

                break;
            case 'list':
                collect($this->configManager->toDotArray())->each(function ($value, $key): void {
                    $this->line("[<comment>$key</comment>] <info>{$this->valueToArg($value)}</info>");
                });

                break;
            case 'edit':
                $editor = value(function () {
                    if ($editor = $this->option('editor')) {
                        return $editor;
                    }

                    $editors = windows_os() ? self::WINDOWS_EDITORS : self::UNIX_EDITORS;
                    $executableFinder = new ExecutableFinder();
                    foreach ($editors as $editor) {
                        if ($executableFinder->find($editor)) {
                            return $editor;
                        }
                    }

                    throw new RuntimeException('Unable to find a default editor or specify the editor.');
                });

                tap(new Process([$editor, $file]), static function (Process $process): void {
                    windows_os() or $process->setTty(true);
                })->setTimeout(null)->mustRun();

                break;
            default:
                throw UnsupportedConfigActionException::make($action);
        }

        $this->output->success('Operated successfully.');

        return self::SUCCESS;
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress InvalidArgument
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('action')) {
            $suggestions->suggestValues(self::ACTIONS);

            return;
        }

        if ($input->mustSuggestArgumentValuesFor('key')) {
            $suggestions->suggestValues(array_keys($this->configManager->toDotArray()));

            return;
        }

        if ($input->mustSuggestOptionValuesFor('editor')) {
            $suggestions->suggestValues(self::UNIX_EDITORS);
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
            new InputArgument('action', InputArgument::REQUIRED, sprintf('The action(<comment>[%s]</comment>) name', implode(', ', self::ACTIONS))),
            new InputArgument('key', InputArgument::OPTIONAL, 'The key of config options'),
            new InputArgument('value', InputArgument::OPTIONAL, 'The value of config options'),
            new InputOption('global', 'g', InputOption::VALUE_NONE, 'Apply to the global config file'),
            new InputOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Apply to the specify config file'),
            new InputOption('editor', 'e', InputOption::VALUE_OPTIONAL, 'Specify editor'),
        ]);
    }

    /**
     * @throws \JsonException
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        if (! file_exists(ConfigManager::globalPath())) {
            $this->configManager->putGlobal();
        }
    }

    /**
     * @throws \JsonException
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function argToValue(string $arg)
    {
        if (0 === strncasecmp($arg, 'null', 4)) {
            return;
        }

        if (0 === strncasecmp($arg, 'true', 4)) {
            return true;
        }

        if (0 === strncasecmp($arg, 'false', 5)) {
            return false;
        }

        if (is_numeric($arg)) {
            return str_contains($arg, '.') ? (float) $arg : (int) $arg;
        }

        if (str($arg)->isJson()) {
            return json_decode($arg, true, 512, JSON_THROW_ON_ERROR);
        }

        return $arg;
    }

    /**
     * @param mixed $value
     *
     * @noinspection DebugFunctionUsageInspection
     * @noinspection JsonEncodingApiUsageInspection
     */
    private function valueToArg($value): string
    {
        if (null === $value) {
            return 'null';
        }

        if (\is_scalar($value)) {
            return var_export($value, true);
        }

        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
