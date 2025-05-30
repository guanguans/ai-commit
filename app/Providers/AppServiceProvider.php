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

namespace App\Providers;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use LaravelZero\Framework\Application;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method void configureIO(InputInterface $input, OutputInterface $output)
 */
final class AppServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection GlobalVariableUsageInspection
     */
    public function register(): void
    {
        /**
         * @see \Rector\Console\Style\SymfonyStyleFactory
         */
        $this->app->singletonIf(
            OutputStyle::class,
            static function (): OutputStyle {
                // to prevent missing argv indexes
                if (!isset($_SERVER['argv'])) {
                    $_SERVER['argv'] = [];
                }

                $argvInput = new ArgvInput;
                $consoleOutput = new ConsoleOutput;

                // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
                (fn () => $this->configureIO($argvInput, $consoleOutput))->call(new ConsoleApplication);

                return new OutputStyle($argvInput, $consoleOutput);
            }
        );

        $this->app->singleton(
            ConsoleLogger::class,
            static fn (Application $app): ConsoleLogger => new ConsoleLogger($app->make(OutputStyle::class))
        );
    }

    public function boot(): void
    {
        // ...
    }
}
