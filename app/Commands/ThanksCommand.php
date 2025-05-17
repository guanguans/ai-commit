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

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

final class ThanksCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'thanks';

    /**
     * @var string
     */
    protected $description = 'Thanks for using this tool.';

    public function handle(): void
    {
        $wantsToSupport = $this->ask('Can you quickly <options=bold>star our GitHub repository</>? 🙏🏻', 'yes');

        if (str($wantsToSupport)->trim()->is(['yes', 'y'])) {
            PHP_OS_FAMILY === 'Darwin' and exec('open https://github.com/guanguans/ai-commit');
            PHP_OS_FAMILY === 'Windows' and exec('start https://github.com/guanguans/ai-commit');
            PHP_OS_FAMILY === 'Linux' and exec('xdg-open https://github.com/guanguans/ai-commit');
        }

        $this->output->writeln([
            '',
            sprintf('  - Star or contribute to <comment>%s</comment>:', config('app.name')),
            '    <options=bold>https://github.com/guanguans/ai-commit</>',
        ]);
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
