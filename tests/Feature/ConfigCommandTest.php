<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
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
    ->with(['null', 'true', 'false', '0.0', '0', json_encode(['foo' => 'bar'], JSON_THROW_ON_ERROR)]);

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
        '--editor' => 'no-editor',
    ]);
})
    ->skip(windows_os(), 'Github action does not support.')
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class);

it('will throw another `Command not found` ProcessFailedException for edit config', function (): void {
    app()->singleton(ExecutableFinder::class, static function () {
        $mockExecutableFinder = \Mockery::mock(ExecutableFinder::class);
        $mockExecutableFinder->allows('find')->andReturn('no-editor');

        return $mockExecutableFinder;
    });

    $this->artisan(ConfigCommand::class, [
        'action' => 'edit',
    ]);
})
    ->skip(windows_os(), 'Github action does not support.')
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
