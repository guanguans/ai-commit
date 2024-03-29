<?php

/** @noinspection NullPointerExceptionInspection */

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
})->group(__DIR__, __FILE__)->with('InvalidJsons');

it('can fix invalid json with missing value', function (): void {
    expect(new JsonFixer())
        ->missingValue('')
        ->fix(substr(json_encode([1, 2, 3]), 0, 5))->toBeJson();

    expect(new JsonFixer())
        ->silent()
        ->fix($json = substr(json_encode([1, 2, 3]), 3))->toBe($json)
        ->not->toBeJson();
})->group(__DIR__, __FILE__);

it('will throw RuntimeException', function (): void {
    (new JsonFixer())
        ->silent(false)
        ->fix(substr(json_encode([1, 2, 3]), 3));
})->group(__DIR__, __FILE__)->throws(RuntimeException::class, 'Could not fix JSON (tried padding ``)');
