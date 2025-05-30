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
use Composer\XdebugHandler\XdebugHandler;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Log\LogManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Intonate\TinkerZero\TinkerZeroServiceProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;

return Application::configure(basePath: \dirname(__DIR__))
    ->withSingletons([
        GeneratorManager::class,
    ])
    ->booting(static function (): void {
        ConfigManager::load();
    })
    ->booting(static function (Application $app): void {
        if (class_exists(TinkerZeroServiceProvider::class) && !$app->isProduction()) {
            $app->register(TinkerZeroServiceProvider::class);
        }
    })
    ->booting(static function (Application $app): void {
        $app->extend(
            LogManager::class,
            static fn (
                LoggerInterface $logger,
                Application $app
            ): LogManager => $logger instanceof LogManager ? $logger : new LogManager($app)
        );
    })
    ->booted(static function (): void {
        collect(Artisan::all())
            ->each(
                static fn (SymfonyCommand $command): SymfonyCommand => $command
                    ->addOption('xdebug', null, InputOption::VALUE_NONE, 'Display xdebug output')
                    ->addOption(
                        'configuration',
                        null,
                        InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                        'Used to dynamically pass one or more configuration key-value pairs(e.g. `--configuration=app.name=guanguans` or `--configuration app.name=guanguans`).',
                    )
            )
            ->tap(static function (): void {
                /**
                 * @see \Illuminate\Foundation\Console\Kernel::rerouteSymfonyCommandEvents()
                 * @see \Rector\Console\ConsoleApplication::doRun()
                 */
                Event::listen(CommandStarting::class, static function (CommandStarting $commandStarting): void {
                    $isXdebugAllowed = $commandStarting->input->hasParameterOption('--xdebug') || app()->runningUnitTests();

                    if (!$isXdebugAllowed) {
                        $xdebugHandler = new XdebugHandler(config('app.name'));
                        $xdebugHandler->setPersistent();
                        $xdebugHandler->check();
                        unset($xdebugHandler);
                    }

                    collect($commandStarting->input->getOption('configuration'))
                        // ->dump()
                        ->mapWithKeys(static function (string $configuration): array {
                            \assert(
                                str_contains($configuration, '='),
                                "The configureable option [$configuration] must be formatted as key=value."
                            );

                            [$key, $value] = str($configuration)->explode('=', 2)->all();

                            return [$key => $value];
                        })
                        ->tap(static fn (Collection $configuration): mixed => config($configuration->all()));
                });
            });
    })
    ->withExceptions(static function (Exceptions $exceptions): void {
        $exceptions->map(
            ValidationException::class,
            fn (ValidationException $validationException) => (function (): ValidationException {
                $this->message = \PHP_EOL.($prefix = '- ').implode(\PHP_EOL.$prefix, $this->validator->errors()->all());

                return $this;
            })->call($validationException)
        );

        $exceptions->reportable(static fn (Throwable $throwable): bool => !Phar::running())->stop();
    })
    ->create()
    ->tap(static function (Application $app): void {
        // $app->afterLoadingEnvironment((new DefineTraceIdListener)(...));
        $app->call(DefineTraceIdListener::class);
    });
