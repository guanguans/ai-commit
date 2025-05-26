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
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singletonIf(
            OutputStyle::class,
            static fn (): OutputStyle => new OutputStyle(new ArgvInput, new ConsoleOutput)
        );
    }

    public function boot(): void
    {
        // ...
    }
}
