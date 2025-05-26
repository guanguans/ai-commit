<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection PhpUnused */
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

use App\Commands\CommitCommand;
use App\Exceptions\RuntimeException;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

it('can from shell commandline create process', function (): void {
    $createProcess = fn () => $this->createProcess('git status');
    expect($createProcess->call(app(CommitCommand::class)))->toBeInstanceOf(Process::class);
})->group(__DIR__, __FILE__)->skip();

it('will throw RuntimeException(not a git repository)', function (): void {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('../'),
        '--config' => config_path('ai-commit.php'),
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class, 'fatal: ');

it('will throw RuntimeException(no cached files to commit)', function (): void {
    // 重置暂存区
    Process::fromShellCommandline('git reset', repository_path())->mustRun();

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
        '--generator' => 'openai',
    ]);
})
    ->depends('it will throw RuntimeException(not a git repository)')
    ->group(__DIR__, __FILE__)
    ->throws(RuntimeException::class, 'There are no cached files to commit. Try running `git add` to cache some files.');

it('can generate and commit message', function (array $parameters): void {
    // 添加文件到暂存区
    file_put_contents(repository_path('playground.random'), Str::random());
    Process::fromShellCommandline('git rm -rf --cached repository/', fixtures_path())->run();
    Process::fromShellCommandline('git add playground.random', repository_path())->mustRun();

    // 设置 git 信息
    Process::fromShellCommandline('git config user.email yaozm', repository_path())->mustRun();
    Process::fromShellCommandline('git config user.name ityaozm@gmail.com', repository_path())->mustRun();
    setup_http_fake();

    // $message = collect([
    //     'subject' => 'Fix(OpenAIGenerator): Debugging output',
    //     '' => null,
    //     'body' => '- Add var_dump() for debugging output- Add var_dump() for stream response',
    // ]);

    $this
        ->artisan(CommitCommand::class, $parameters + [
            'path' => repository_path(),
            '--generator' => 'openai',
            '--no-edit' => true,
            '--no-verify' => true,
            '--verbose' => true,
        ])
        // ->expectsTable(
        //     $message->keys()->all(),
        //     [$message->all()]
        // )
        // ->expectsChoice('Please choice commit type', array_key_first($types = config('ai-commit.types')), $types)
        ->expectsQuestion('Please choice commit type', array_key_first(config('ai-commit.types')))
        // ->expectsChoice('Please choice a commit message', $message->pluck('subject', 'id')->first(), $message->pluck('subject', 'id')->all())
        // ->expectsQuestion('Please choice a commit message', '<comment>regenerating...</comment>')
        ->expectsConfirmation('Do you want to commit this message?', 'yes')
        ->assertSuccessful();
})
    ->with('commit command parameters')
    ->depends('it will throw RuntimeException(no cached files to commit)')
    ->group(__DIR__, __FILE__);

afterAll(static function (): void {
    // 清理 playground 仓库
    Process::fromShellCommandline('git reset $(git rev-list --max-parents=0 HEAD)', repository_path())->run();
    // Process::fromShellCommandline('git checkout -- .', repository_path())->run();
    Process::fromShellCommandline('git checkout HEAD -- .', repository_path())->run();
    Process::fromShellCommandline('git add tests/Fixtures/repository/', base_path())->mustRun();
});
