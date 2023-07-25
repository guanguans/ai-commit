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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;

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

it('will throw `Command not found` ProcessFailedException for edit config', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
        '--editor' => 'foo',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class);

it('will throw another `Command not found` ProcessFailedException for edit config', function (): void {
    app()->singleton(ExecutableFinder::class, static function () {
        $mockExecutableFinder = \Mockery::mock(ExecutableFinder::class);
        $mockExecutableFinder->allows('find')->andReturn('foo');

        return $mockExecutableFinder;
    });

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class);

it('will throw RuntimeException for edit config', function (): void {
    app()->singleton(ExecutableFinder::class, static function () {
        $mockExecutableFinder = \Mockery::mock(ExecutableFinder::class);
        $mockExecutableFinder->allows('find')->andReturnNull();

        return $mockExecutableFinder;
    });

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(RuntimeException::class, 'Unable to find a default editor or specify the editor.');

it('will throw UnsupportedConfigActionException', function (): void {
    $this->artisan(ConfigCommand::class, [
        'action' => 'foo',
    ]);
})->group(__DIR__, __FILE__)->throws(UnsupportedConfigActionException::class, 'foo');
