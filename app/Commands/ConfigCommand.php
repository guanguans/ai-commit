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

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ConfigCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = '
        config
        {--set= : set config value}
        {--get= : get config value}
        {--edit : Specify edit}
        {--editor=nano : Specify editor}
        {--g|global : Specify global}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Manage project configuration';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var \App\ConfigManager $config */
        $config = $this->laravel->get('config')->get('ai-commit');
        $config->toCwd();
        dd($this->options(), $this->arguments());
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
