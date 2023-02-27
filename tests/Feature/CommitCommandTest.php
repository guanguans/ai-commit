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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Process\Process;

it('will throw TaskException(not a git repository)', function () {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('../'),
        '--config' => config_path('ai-commit.php'),
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'fatal: ');

it('will throw TaskException(no staged files to commit)', function () {
    // 重置暂存区
    Process::fromShellCommandline('git reset', repository_path())->mustRun();

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
    ]);
})
    ->depends('it will throw TaskException(not a git repository)')
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'There are no staged files to commit. Try running `git add` to stage some files.');

it('will throw TaskException(The generated commit messages is an invalid JSON)', function () {
    // 添加文件到暂存区
    file_put_contents(repository_path('playground.random'), Str::random());
    Process::fromShellCommandline('git rm -rf --cached repository/', fixtures_path())->mustRun();
    Process::fromShellCommandline('git add playground.random', repository_path())->mustRun();

    Http::fake(function () {
        return Http::response([
            'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
            'object' => 'text_completion',
            'created' => 1677143178,
            'model' => 'text-davinci-003',
            'choices' => [
                0 => [
                    'text' => 'invalid json', // 无效响应
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
    ]);
})
    ->depends('it will throw TaskException(no staged files to commit)')
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'The generated commit messages(');

it('can generate and commit messages', function () {
    // 设置 git 信息
    Process::fromShellCommandline('git config user.email yaozm', repository_path())->mustRun();
    Process::fromShellCommandline('git config user.name ityaozm@gmail.com', repository_path())->mustRun();
    setup_http_fake();

    $messages = collect([
        [
            'id' => 1,
            'subject' => 'Fix(OpenAIGenerator): Debugging output',
            'body' => '- Add var_dump() for debugging output- Add var_dump() for stream response',
        ],
        [
            'id' => 2,
            'subject' => 'Refactor(OpenAIGenerator): Error handling',
            'body' => '- Check for error response in JSON- Handle error response',
        ],
        [
            'id' => 3,
            'subject' => 'Docs(OpenAIGenerator): Update documentation',
            'body' => '- Update documentation for OpenAIGenerator class',
        ],
    ]);

    $this
        ->artisan(CommitCommand::class, [
            'path' => repository_path(),
            '--no-edit' => true,
            '--verbose' => true,
        ])
        ->expectsTable(
            array_keys($messages->first()),
            $messages->chunk(1)
                ->transform(function (Collection $messages) {
                    return $messages->prepend(new TableSeparator());
                })
                ->flatten(1)
                ->skip(1)
        )
        // ->expectsChoice('Please choice a commit message', $messages->pluck('subject', 'id')->first(), $messages->pluck('subject', 'id')->all())
        ->expectsQuestion('Please choice a commit message', $messages->pluck('subject', 'id')->first())
        ->assertSuccessful();
})
    ->depends('it will throw TaskException(The generated commit messages is an invalid JSON)')
    ->group(__DIR__, __FILE__);

afterAll(static function (): void {
    // 清理 playground 仓库
    Process::fromShellCommandline('git reset b7a6f28', repository_path())->run();
    Process::fromShellCommandline('git checkout -- .', repository_path())->run();
    Process::fromShellCommandline('git add tests/Fixtures/repository/', base_path())->mustRun();
});
