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

use App\ConfigManager;
use App\GeneratorManager;
use App\Mixins\StringableMixin;
use App\Mixins\StrMixin;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\BindingResolutionException;
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
    /** @var array<array-key, string> */
    public array $singletons = [
        GeneratorManager::class => GeneratorManager::class,
        StringableMixin::class => StringableMixin::class,
        StrMixin::class => StrMixin::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->singletonIf(OutputStyle::class, static fn (): OutputStyle => new OutputStyle(new ArgvInput, new ConsoleOutput));

        Str::mixin($this->app->make(StrMixin::class));
        Stringable::mixin($this->app->make(StringableMixin::class));
    }

    /**
     * @throws \JsonException
     */
    public function boot(): void
    {
        /** @see \Symfony\Component\VarDumper\VarDumper */
        /** @noinspection GlobalVariableUsageInspection */
        $_SERVER['VAR_DUMPER_FORMAT'] = 'server';

        $this->app->extend(LoggerInterface::class, static fn (LoggerInterface $logger): NullLogger =>
            // return $logger instanceof ConsoleLogger ? $logger : new ConsoleLogger(app(OutputStyle::class));
            $logger instanceof NullLogger ? $logger : new NullLogger);

        ConfigManager::load();
    }
}
