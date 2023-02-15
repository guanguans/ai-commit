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

final class ThanksCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'thanks';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Thanks for using this tool.';

    public function handle(): int
    {
        $wantsToSupport = $this->ask('Can you quickly <options=bold>star our GitHub repository</>? 🙏🏻', 'yes');

        if (\str($wantsToSupport)->trim()->is(['yes', 'y'])) {
            PHP_OS_FAMILY === 'Darwin' and exec('open https://github.com/guanguans/ai-commit');
            PHP_OS_FAMILY === 'Windows' and exec('start https://github.com/guanguans/ai-commit');
            PHP_OS_FAMILY === 'Linux' and exec('xdg-open https://github.com/guanguans/ai-commit');
        }

        $this->output->writeln([
            '',
            sprintf('  - Star or contribute to <comment>%s</comment>:', config('app.name')),
            '    <options=bold>https://github.com/guanguans/ai-commit</>',
        ]);

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
