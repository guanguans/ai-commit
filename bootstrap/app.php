<?php

/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedNamespaceInspection */
/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\Application;
use App\ConfigManager;
use App\GeneratorManager;
use App\Listeners\DefineTraceIdListener;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Log\LogManager;
use Illuminate\Validation\ValidationException;
use Intonate\TinkerZero\TinkerZeroServiceProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

return Application::configure(basePath: \dirname(__DIR__))
    ->booting(static function (): void {
        ConfigManager::load();
    })
    // ->booted(static function (Application $app): void {
    //     if (class_exists(TinkerZeroServiceProvider::class) && !$app->isProduction()) {
    //         $app->register(TinkerZeroServiceProvider::class);
    //     }
    // })
    ->booted(static function (Application $app): void {
        // new ConsoleLogger(app(OutputStyle::class));
        $app->extend(
            LogManager::class,
            static fn (
                LoggerInterface $logger,
                Application $app
            ): LogManager => $logger instanceof LogManager ? $logger : new LogManager($app)
        );
    })
    ->withSingletons([
        GeneratorManager::class,
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
    ->create()
    ->tap(static function (Application $app): void {
        $app->afterLoadingEnvironment((new DefineTraceIdListener)(...));
    });
