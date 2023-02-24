<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Commands\CommitCommand;
use App\Exceptions\TaskException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

beforeEach(function () {
    config('ai-commit')->set('generators.openai.api_key', 'sk-...');
});

it('will throw `TaskException(not a git repository)` ', function () {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('../'),
    ]);
})->group(__DIR__, __FILE__)->throws(TaskException::class, 'fatal: ');

it('will throw `TaskException(no staged files to commit)` ', function () {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('tests/Fixtures/repository'),
    ]);
})->group(__DIR__, __FILE__)->throws(TaskException::class, 'There are no staged files to commit. Try running `git add` to stage some files.');

// it('will throw `TaskException(no commit messages generated)` ', function () {
//     Http::fake([
//         'https://api.openai.com/v1/completions' => Http::response(['foo' => 'bar']),
//     ]);
//
//     $this->getFunctionMock(class_namespace(Process::class), 'stream_get_contents')
//         ->expects($this->any())
//         ->willReturn('git diff');
//
//     $this->artisan(CommitCommand::class);
// })->group(__DIR__, __FILE__)->throws(TaskException::class, 'No commit messages generated.');

// it('will throw `TaskException(The generated commit messages is an invalid JSON.)` ', function () {
//     Http::fake([
//         'https://api.openai.com/v1/completions' => Http::response(['foo' => 'bar']),
//     ]);
//
//     $this->getFunctionMock(class_namespace(Process::class), 'stream_get_contents')
//         ->expects($this->any())
//         ->willReturn('git diff');
//
//     $this->artisan(CommitCommand::class);
// })->group(__DIR__, __FILE__)->throws(TaskException::class, 'The generated commit messages is an invalid JSON.');
