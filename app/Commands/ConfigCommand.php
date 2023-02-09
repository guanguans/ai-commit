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
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ConfigCommand extends Command
{
    public const ACTIONS = ['set', 'get', 'unset', 'list', 'edit'];

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Manage config options.';

    /**
     * @var \App\ConfigManager
     */
    protected $configManager;

    public function __construct()
    {
        $this->configManager = config('ai-commit');
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('action', InputArgument::REQUIRED, sprintf('The action(<comment>[%s]</comment>) name', implode(', ', self::ACTIONS))),
            new InputArgument('key', InputArgument::OPTIONAL, 'The key of config options'),
            new InputArgument('value', InputArgument::OPTIONAL, 'The value of config options'),
            new InputOption('global', 'g', InputOption::VALUE_NONE, 'Apply to the global config file'),
            new InputOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Apply to the specify config file'),
            new InputOption('editor', 'e', InputOption::VALUE_OPTIONAL, 'Specify editor', null),
        ]);
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if (! file_exists($this->configManager::globalPath())) {
            $this->configManager->toGlobal();
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

        $this->info("The config file($file) is being operated");
        file_exists($file) or $this->configManager->toFile($file);
        $this->configManager->replaceFrom($file);
        $action = $this->argument('action');
        $key = $this->argument('key');

        if (in_array($action, ['unset', 'set'], true) && null === $key) {
            $this->error('Please specify the parameter key');

            return self::FAILURE;
        }

        switch ($action) {
            case 'set':
                $this->configManager->set($key, $this->argument('value'));
                $this->configManager->toFile($file);

                break;
            case 'get':
                $value = null === $key ? $this->configManager->toJson() : $this->configManager->get($key);
                $value = transform($value, $transform = function ($value) {
                    if (is_string($value)) {
                        return $value;
                    }

                    if (null === $value) {
                        return 'null';
                    }

                    if (is_scalar($value)) {
                        return (string) \str(json_encode([$value], JSON_UNESCAPED_UNICODE))->replaceFirst('[', '')->replaceLast(']', '');
                    }

                    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }, $transform);

                $this->line('');
                $this->line($value);

                break;
            case 'unset':
                $this->configManager->forget($key);
                $this->configManager->toFile($file);

                break;
            case 'list':
                $flattenWithKeys = static function (array $array, string $delimiter = '.', $prefixKey = null) use (&$flattenWithKeys): array {
                    $result = [];
                    foreach ($array as $key => $item) {
                        $fullKey = null === $prefixKey ? $key : $prefixKey.$delimiter.$key;
                        is_array($item) ? $result += $flattenWithKeys($item, $delimiter, $fullKey) : $result[$fullKey] = $item;
                    }

                    return $result;
                };

                $json = ConfigManager::create($flattenWithKeys($this->configManager->all()))->toJson();

                $this->line('');
                \str($json)->ltrim('{')->rtrim('}')->explode(','.PHP_EOL)->each(function (string $line) {
                    $this->line(trim($line));
                });

                break;
            case 'edit':
                $editor = value(function () {
                    if ($editor = $this->option('editor')) {
                        return $editor;
                    }

                    if (windows_os()) {
                        return 'notepad';
                    }

                    foreach (['editor', 'vim', 'vi', 'nano', 'pico', 'ed'] as $editor) {
                        if (exec("which $editor")) {
                            return $editor;
                        }
                    }

                    throw new \RuntimeException('No editor found or specified.');
                });

                Process::fromShellCommandline("$editor $file")->setTimeout(null)->setTty(true)->mustRun();

                break;
            default:
                throw new \RuntimeException(sprintf('The action(%s) must be one of [set, get, unset, list, edit].', implode(', ', self::ACTIONS)));
        }

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
