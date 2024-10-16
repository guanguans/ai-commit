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
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\GeneratorManager;

beforeEach(function (): void {
    /** @var \App\Generators\GithubCopilotCliGenerator $generator */
    $generator = app(GeneratorManager::class)->driver('github_copilot_cli');
    $this->generator = $generator;
});

it('can run string cmd', function (): void {
    expect(
        (function () {
            return $this->runProcess('echo foo');
        })->call($this->generator)
    )->isSuccessful()->toBeTrue();
})->group(__DIR__, __FILE__);
