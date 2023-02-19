<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Providers;

use App\ConfigManager;
use App\GeneratorManager;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public $singletons = [
        StringableMacro::class => StringableMacro::class,
        StrMacro::class => StrMacro::class,
        GeneratorManager::class => GeneratorManager::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /** @see \Symfony\Component\VarDumper\VarDumper */
        $_SERVER['VAR_DUMPER_FORMAT'] = 'server';

        ConfigManager::load();
    }

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singletonIf(OutputStyle::class, function () {
            return new OutputStyle(new ArgvInput(), new ConsoleOutput());
        });

        Stringable::mixin($this->app->make(StringableMacro::class));
        Str::mixin($this->app->make(StrMacro::class));
    }
}
