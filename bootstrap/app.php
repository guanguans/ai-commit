<?php

/** @noinspection PhpUndefinedNamespaceInspection */
/** @noinspection PhpUndefinedClassInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\ConfigManager;
use App\GeneratorManager;
use App\Mixins\StringableMixin;
use App\Mixins\StrMixin;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Log\LogManager;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\ValidationException;
use Intonate\TinkerZero\TinkerZeroServiceProvider;
use LaravelZero\Framework\Application;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\VarDumper\VarDumper;

return Application::configure(basePath: \dirname(__DIR__))
    ->booting(static function (): void {
        /** @see \Symfony\Component\VarDumper\VarDumper */
        /** @noinspection GlobalVariableUsageInspection */
        $_SERVER['VAR_DUMPER_FORMAT'] = null;

        VarDumper::setHandler(null);

        /** @noinspection GlobalVariableUsageInspection */
        $_SERVER['VAR_DUMPER_FORMAT'] = 'server';

        // $_SERVER['VAR_DUMPER_SERVER'] = '0.0.0.0:9912';
    })
    ->booting(static function (Application $application): void {
        Str::mixin($application->make(StrMixin::class));
        Stringable::mixin($application->make(StringableMixin::class));
    })
    ->booting(static function (Application $application): void {
        $application->extend(
            LoggerInterface::class,
            // $logger instanceof ConsoleLogger ? $logger : new ConsoleLogger(app(OutputStyle::class));
            static fn (LoggerInterface $logger): NullLogger => $logger instanceof NullLogger ? $logger : new NullLogger
        );
    })
    ->booting(static function (): void {
        ConfigManager::load();
    })
    // ->booted(static function (Application $app): void {
    //     if (class_exists(TinkerZeroServiceProvider::class) && !$app->isProduction()) {
    //         $app->register(TinkerZeroServiceProvider::class);
    //     }
    // })
    // ->booted(static function (Application $app): void {
    //     $app->extend(LogManager::class, static function (LoggerInterface $logger, Application $application) {
    //         if (!$logger instanceof LogManager) {
    //             return new LogManager($application);
    //         }
    //
    //         return $logger;
    //     });
    // })
    ->withSingletons([
        GeneratorManager::class,
        StringableMixin::class,
        StrMixin::class,
    ])
    ->withExceptions(static function (Exceptions $exceptions): void {
        $exceptions->map(
            ValidationException::class,
            fn (ValidationException $validationException) => (function (): ValidationException {
                $this->message = \PHP_EOL.($prefix = '- ').implode(\PHP_EOL.$prefix, $this->validator->errors()->all());

                return $this;
            })->call($validationException)
        );

        $exceptions->reportable(static fn (Throwable $throwable): bool => !Phar::running());
        $exceptions->reportable(static fn (Throwable $throwable): bool => false)->stop();
    })
    ->create();
