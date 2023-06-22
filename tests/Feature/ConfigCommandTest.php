<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Commands\ConfigCommand;
use App\ConfigManager;
use App\Exceptions\RuntimeException;
use App\Exceptions\UnsupportedConfigActionException;
use Symfony\Component\Process\Exception\RuntimeException as SymfonyRuntimeException;
use Symfony\Component\Process\Process;

it('can set config', function (): void {
    $this->getFunctionMock(class_namespace(ConfigCommand::class), 'file_exists')
        ->expects($this->atLeastOnce())
        ->willReturn(false);

    $this->artisan(ConfigCommand::class, [
        'action' => 'set',
        'key' => 'foo.bar',
        'value' => 'bar',
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'set',
        'key' => 'foo.bar',
        'value' => 'bar',
        '--file' => repository_path(ConfigManager::NAME),
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'set',
        'key' => 'foo.bar',
        'value' => 'bar',
        '--global' => true,
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'set',
    ])->assertFailed();
})->group(__DIR__, __FILE__);

it('can set/get special config value', function ($value): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'set',
        'key' => 'foo.bar',
        'value' => $value,
        '--file' => repository_path(ConfigManager::NAME),
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'get',
        'key' => 'foo.bar',
        '--file' => repository_path(ConfigManager::NAME),
    ])->assertSuccessful();
})
    ->group(__DIR__, __FILE__)
    ->with(['null', 'true', 'false', '0.0', '0', json_encode(['foo' => 'bar'])]);

it('can get config', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'get',
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'get',
        'key' => 'foo',
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'get',
        'key' => 'generators.openai',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('can unset config', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'unset',
        'key' => 'foo.bar',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('can reset config', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'reset',
        'key' => 'foo.bar',
    ])->assertSuccessful();

    $this->artisan(ConfigCommand::class, [
        'action' => 'reset',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('can list config', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'list',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('will throw RuntimeException for edit config on Windows', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->skip(! windows_os())
    ->throws(RuntimeException::class, 'The edit config command is not supported on Windows.');

it('will throw RuntimeException for edit config', function (): void {
    $this->getFunctionMock(class_namespace(ConfigCommand::class), 'exec')
        ->expects($this->exactly(6))
        ->willReturn('');

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->skip(windows_os())
    ->throws(RuntimeException::class, 'No editor found or specified.');

it('will throw \SymfonyRuntimeException for edit config', function (): void {
    $this->getFunctionMock(class_namespace(Process::class), 'proc_open')
        ->expects($this->once())
        ->willReturn(false);

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
        '--editor' => 'foo',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->skip()
    ->throws(SymfonyRuntimeException::class, 'TTY mode requires /dev/tty to be read/writable.');

it('will throw SymfonyRuntimeException for edit config', function (): void {
    $this->getFunctionMock(class_namespace(ConfigCommand::class), 'exec')
        ->expects($this->once())
        ->willReturn('/usr/local/bin/vim');

    $this->getFunctionMock(class_namespace(Process::class), 'proc_open')
        ->expects($this->any())
        ->willReturn(false);

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->skip(windows_os())
    ->throws(SymfonyRuntimeException::class, 'TTY mode requires /dev/tty to be read/writable.');

it('will throw UnsupportedConfigActionException', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'foo',
    ]);
})->group(__DIR__, __FILE__)->throws(UnsupportedConfigActionException::class, 'foo');
