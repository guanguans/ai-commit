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

it('can set config', function () {
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
        '--file' => __DIR__.DIRECTORY_SEPARATOR.ConfigManager::NAME,
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

it('can get config', function () {
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

it('can unset config', function () {
    $this->artisan(ConfigCommand::class, [
        'action' => 'unset',
        'key' => 'foo.bar',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('can list config', function () {
    $this->artisan(ConfigCommand::class, [
        'action' => 'list',
    ])->assertSuccessful();
})->group(__DIR__, __FILE__);

it('will throw RuntimeException for edit config', function () {
    $this->getFunctionMock(class_namespace(ConfigCommand::class), 'exec')
        ->expects($this->exactly(6))
        ->willReturn('');

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})->group(__DIR__, __FILE__)->throws(RuntimeException::class, 'No editor found or specified.');

it('will throw \SymfonyRuntimeException for edit config', function () {
    $this->getFunctionMock(class_namespace(Process::class), 'proc_open')
        ->expects($this->once())
        ->willReturn(false);

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
        '--editor' => 'foo',
    ]);
})->skip()->group(__DIR__, __FILE__)->throws(SymfonyRuntimeException::class, 'TTY mode requires /dev/tty to be read/writable.');

it('will throw SymfonyRuntimeException for edit config', function () {
    $this->getFunctionMock(class_namespace(ConfigCommand::class), 'exec')
        ->expects($this->once())
        ->willReturn('/usr/local/bin/vim');

    $this->getFunctionMock(class_namespace(Process::class), 'proc_open')
        ->expects($this->any())
        ->willReturn(false);

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})->group(__DIR__, __FILE__)->throws(SymfonyRuntimeException::class, 'TTY mode requires /dev/tty to be read/writable.');

it('will throw UnsupportedConfigActionException', function () {
    $this->artisan(ConfigCommand::class, [
        'action' => 'foo',
    ]);
})->group(__DIR__, __FILE__)->throws(UnsupportedConfigActionException::class, 'foo');
