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
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    setup_http_fake();
});

it('can generate commit messages', function (): void {
    expect(app(GeneratorManager::class)->driver('ernie_bot_turbo'))
        ->generate('OK')->toBeString()->not->toBeEmpty();
    // Http::assertSentCount(2);
})->group(__DIR__, __FILE__);
