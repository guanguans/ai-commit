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
use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function (): void {
});

it('throws `ProcessFailedException`', function (): void {
    config('ai-commit')->set('generators.bito_cli.binary', 'bito-cli');
    expect(app(GeneratorManager::class)->driver('bito_cli'))->generate('error');
})->group(__DIR__, __FILE__)->throws(ProcessFailedException::class);
