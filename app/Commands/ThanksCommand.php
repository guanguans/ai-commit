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
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $signature = 'thanks';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $description = 'Thanks for using this tool.';

    public function handle(): void
    {
        $wantsToSupport = $this->ask('Can you quickly <options=bold>star our GitHub repository</>? 🙏🏻', 'yes');

        if (str($wantsToSupport)->trim()->is(['yes', 'y'])) {
            exec(match (\PHP_OS_FAMILY) {
                'Darwin' => 'open https://github.com/guanguans/ai-commit',
                'Windows' => 'start https://github.com/guanguans/ai-commit',
                default => 'xdg-open https://github.com/guanguans/ai-commit',
            });
        }

        $this->output->writeln([
            '',
            \sprintf('  - Star or contribute to <comment>%s</comment>:', config('app.name')),
            '    <options=bold>https://github.com/guanguans/ai-commit</>',
        ]);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
