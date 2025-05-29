<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnused */
/** @noinspection SqlResolve */
declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\GeneratorManager;

beforeEach(function (): void {
    /** @var \App\Generators\GithubCopilotCliGenerator $generator */
    $generator = app(GeneratorManager::class)->driver('github_copilot_cli');
    $this->generator = $generator;
});

it('can run string cmd', function (): void {
    expect(
        (fn () => $this->runProcess('echo foo'))->call($this->generator)
    )->isSuccessful()->toBeTrue();
})->group(__DIR__, __FILE__);
