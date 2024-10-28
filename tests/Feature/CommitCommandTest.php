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
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

it('can create a process from a shell command', function (): void {
    $createProcess = function () {
        return $this->createProcess('git status');
    };

    expect($createProcess->call(app(CommitCommand::class)))
        ->toBeInstanceOf(Process::class)
        ->withMessage("The created process should be an instance of Symfony's Process class.");
})
    ->group(__DIR__, __FILE__)
    ->skip();

it('throws TaskException for non-git repository', function (): void {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('../'),
        '--config' => config_path('ai-commit.php'),
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class, 'fatal: Not a git repository');

it('throws TaskException when there are no cached files to commit', function (): void {
    resetStagingArea();

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
        '--generator' => 'openai',
    ]);
})
    ->depends('throws TaskException for non-git repository')
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'No cached files to commit. Run `git add` to cache files.');

it('throws TaskException for invalid JSON in commit message', function (): void {
    stageRandomFile();

    Http::fake(function (): PromiseInterface {
        return Http::response([
            'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
            'object' => 'text_completion',
            'created' => 1677143178,
            'model' => 'text-davinci-003',
            'choices' => [
                [
                    'text' => 'invalid json',
                    'index' => 0,
                    'logprobs' => null,
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 749,
                'completion_tokens' => 159,
                'total_tokens' => 908,
            ],
        ]);
    });

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
        '--generator' => 'openai',
    ])
        ->expectsQuestion('Please choose commit type', array_key_first(config('ai-commit.types')))
        ->throws(TaskException::class, 'The generated commit message contains invalid JSON');
})
    ->depends('throws TaskException when there are no cached files to commit')
    ->group(__DIR__, __FILE__);

it('generates and commits a valid message', function (array $parameters): void {
    stageRandomFile();
    setGitUserConfig();

    setup_http_fake();

    $message = collect([
        'subject' => 'Fix(OpenAIGenerator): Debugging output',
        'body' => '- Add var_dump() for debugging output- Add var_dump() for stream response',
    ]);

    $this->artisan(CommitCommand::class, $parameters + [
        'path' => repository_path(),
        '--generator' => 'openai',
        '--no-edit' => true,
        '--no-verify' => true,
        '--verbose' => true,
    ])
        ->expectsTable($message->keys()->all(), [$message->all()])
        ->expectsQuestion('Please choose commit type', array_key_first(config('ai-commit.types')))
        ->expectsConfirmation('Do you want to commit this message?', 'yes')
        ->assertSuccessful();
})
    ->with('commit command parameters')
    ->depends('throws TaskException for invalid JSON in commit message')
    ->group(__DIR__, __FILE__);

afterAll(static function (): void {
    cleanupRepository();
});

// Helper functions
function resetStagingArea(): void {
    Process::fromShellCommandline('git reset', repository_path())->mustRun();
}

function stageRandomFile(): void {
    file_put_contents(repository_path('playground.random'), Str::random());
    Process::fromShellCommandline('git rm -rf --cached repository/', fixtures_path())->mustRun();
    Process::fromShellCommandline('git add playground.random', repository_path())->mustRun();
}

function setGitUserConfig(): void {
    Process::fromShellCommandline('git config user.email "yaozm@example.com"', repository_path())->mustRun();
    Process::fromShellCommandline('git config user.name "ityaoyzm@gmail.com"', repository_path())->mustRun();
}

function cleanupRepository(): void {
    Process::fromShellCommandline('git reset $(git rev-list --max-parents=0 HEAD)', repository_path())->run();
    Process::fromShellCommandline('git checkout HEAD -- .', repository_path())->run();
    Process::fromShellCommandline('git add tests/Fixtures/repository/', base_path())->mustRun();
}
