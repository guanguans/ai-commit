<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\ConfigManager;
use App\Exceptions\UnsupportedConfigActionException;
use Symfony\Component\Process\Exception\ProcessFailedException;

it('action of set', function () {
    $this->artisan('config set foo.bar bar')

        // ->expectsOutput(sprintf('The config file(%s) is being operated', ConfigManager::localPath()))
        ->assertExitCode(0);

    $file = __DIR__.DIRECTORY_SEPARATOR.ConfigManager::NAME;
    $this->artisan(sprintf('config set foo.bar bar --file %s', $file))

        // ->expectsOutput(sprintf("The config file($file) is being operated"))
        ->assertExitCode(0);

    $this->artisan(sprintf('config set foo.bar bar --global'))

        // ->expectsOutput(sprintf('The config file(%s) is being operated', ConfigManager::globalPath()))
        ->assertExitCode(0);

    $this->artisan(sprintf('config set'))

        // ->expectsOutput('Please specify the parameter key.')
        ->assertExitCode(1);
});

it('action of get', function () {
    $this->artisan('config get')->assertExitCode(0);
    $this->artisan('config get foo')->assertExitCode(0);
    $this->artisan('config get generators.openai')->assertExitCode(0);
});

it('action of unset', function () {
    $this->artisan('config unset foo.bar')->assertExitCode(0);
});

it('action of list', function () {
    $this->artisan('config list')->assertExitCode(0);
});

/**
 * @psalm-suppress UnevaluatedCode
 */
it('action of edit', function () {
    $this->markTestSkipped(__METHOD__);

    $this->expectException(ProcessFailedException::class);
    $this->expectExceptionMessage('The command "foo ');
    $this->artisan('config edit --editor=foo');
});

it('unsupported action', function () {
    $this->expectException(UnsupportedConfigActionException::class);
    $this->expectExceptionMessage('foo');
    $this->artisan('config foo');
});
