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
use App\Macros\CollectionMacro;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array<array-key, string>
     */
    public $singletons = [
        CollectionMacro::class => CollectionMacro::class,
        GeneratorManager::class => GeneratorManager::class,
        StringableMacro::class => StringableMacro::class,
        StrMacro::class => StrMacro::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function register(): void
    {
        $this->app->singletonIf(OutputStyle::class, static function (): OutputStyle {
            return new OutputStyle(new ArgvInput(), new ConsoleOutput());
        });

        Collection::mixin($this->app->make(CollectionMacro::class));
        Str::mixin($this->app->make(StrMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
    }

    /**
     * @throws \JsonException
     */
    public function boot(): void
    {
        /** @see \Symfony\Component\VarDumper\VarDumper */
        /** @noinspection GlobalVariableUsageInspection */
        $_SERVER['VAR_DUMPER_FORMAT'] = 'server';

        $this->app->extend(LoggerInterface::class, static function (LoggerInterface $logger): NullLogger {
            // return $logger instanceof ConsoleLogger ? $logger : new ConsoleLogger(app(OutputStyle::class));
            return $logger instanceof NullLogger ? $logger : new NullLogger();
        });

        ConfigManager::load();
    }
}
