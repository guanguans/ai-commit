<?php

/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection DebugFunctionUsageInspection */
/** @noinspection ForgottenDebugOutputInspection */

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

use App\Commands\CommitCommand;

it('can try fix message', function (string $message): void {
    expect($message)->not->toBeJson()
        ->and(
            (function (string $message): string {
                return $this->tryFixMessage($message);
            })->call(app(CommitCommand::class), $message)
        )
        ->when(true, function (Pest\Expectation $expect) use ($message): void {
            dump($message, $expect->value);
        })
        ->toBeJson();
})->group(__DIR__, __FILE__)->with('invalid messages');
