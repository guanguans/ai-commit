<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection DebugFunctionUsageInspection */
/** @noinspection ForgottenDebugOutputInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection PhpInternalEntityUsedInspection */
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
use Pest\Expectation;

it('can try fix message', function (string $message): void {
    expect($message)->not->toBeJson()
        ->and(
            (fn (string $message): string => $this->tryFixMessage($message))->call(app(CommitCommand::class), $message)
        )
        ->when(true, function (Expectation $expect) use ($message): void {
            dump($message, $expect->value);
        })
        ->toBeJson();
})->group(__DIR__, __FILE__)->with('invalid messages');
