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
use Illuminate\Contracts\Config\Repository;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ConfigCommand extends Command
{
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
    protected $description = 'Manage configuration options.';
    /**
     * @var mixed
     */
    protected $configManager;

    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('action', InputArgument::REQUIRED, ''),
            new InputArgument('agr1', InputArgument::OPTIONAL, ''),
            new InputArgument('agr2', InputArgument::OPTIONAL, ''),
            new InputOption('global', 'g', InputOption::VALUE_NONE, 'Apply command to the global config file'),
            new InputOption('editor', 'e', InputOption::VALUE_OPTIONAL, 'Open editor', null),
        ]);
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        /** @var \App\ConfigManager $configManager */
        $this->configManager = $this->laravel->get(Repository::class)->get('ai-commit');
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
            if ($this->option('global')) {
                return ConfigManager::globalPath();
            }

            return ConfigManager::localPath();
        });

        if ($this->option('verbose')) {
            $this->info("The ($file) configuration file is being manipulated");
        }

        if (! file_exists($file)) {
            $confirm = $this->confirm('Whether to create a configuration file?', true);
            $confirm and $this->configManager->toFile($file);
        }

        /** @var \App\ConfigManager $configManager */
        $configManager = file_exists($file) ? ConfigManager::createFrom($file) : ConfigManager::create([]);

        switch ($action = $this->argument('action')) {
            case 'edit':
                $editor = $this->option('editor') ?: value(function () {
                    if ($editor = $this->option('editor')) {
                        return $editor;
                    }

                    if (windows_os()) {
                        return 'notepad';
                    }

                    foreach (['editor', 'vim', 'vi', 'nano', 'pico', 'ed'] as $candidate) {
                        if (exec("which $candidate")) {
                            return $candidate;
                        }
                    }

                    throw new \RuntimeException('No editors were found.');
                });

                Process::fromShellCommandline("$editor $file")->setTty(true)->setTimeout(null)->mustRun();

                break;
            case 'list':
                if ($file) {
                    $configManager = ConfigManager::createFrom($file);
                }

                $this->line($configManager->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                break;
            case 'unset':
                $configManager->forget($this->argument('agr1'));
                $configManager->toFile($file);

                break;
            case 'set':
                $configManager->set($this->argument('agr1'), $this->argument('agr2'));
                $configManager->toFile($file);

                break;
            case 'get':
                $value = transform($configManager->get($this->argument('agr1')), function ($value) {
                    true === $value and $value = 'true';
                    false === $value and $value = 'false';
                    null === $value and $value = 'null';
                    ! is_scalar($value) and $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    return $value;
                });

                $this->line($value);

                break;
            default:
                throw new \RuntimeException("Unexpected action $action");
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
