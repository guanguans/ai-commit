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

use App\Support\JsonFixer;

it('can fix invalid json', function (string $json, string $expect): void {
    expect(new JsonFixer())->fix($json)->toBe($expect);
})->group(__DIR__, __FILE__)->with('invalid jsons');

it('can fix invalid json with missing value', function (): void {
    expect(new JsonFixer())
        ->missingValue('')
        ->fix(substr(json_encode([1, 2, 3], JSON_THROW_ON_ERROR), 0, 5))->toBeJson()
        ->and(new JsonFixer())
        ->silent()
        ->fix($json = substr(json_encode([1, 2, 3], JSON_THROW_ON_ERROR), 3))->toBe($json)
        ->not->toBeJson();
})->group(__DIR__, __FILE__);

it('will throw RuntimeException', function (): void {
    (new JsonFixer())
        ->silent(false)
        ->fix(substr(json_encode([1, 2, 3], JSON_THROW_ON_ERROR), 3));
})->group(__DIR__, __FILE__)->throws(RuntimeException::class, 'Could not fix JSON (tried padding ``)');
