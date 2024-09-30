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
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    setup_http_fake();
});

it('can generate commit messages', function (): void {
    expect(app(GeneratorManager::class)->driver('openai'))
        ->generate('OK')->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('will throw forbidden RequestException', function (): void {
    app(GeneratorManager::class)->driver('openai')->generate('Forbidden');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 403');

it('will throw unauthorized RequestException', function (): void {
    app(GeneratorManager::class)
        ->tap(function (): void {
            // reset_http_fake();
        })
        ->driver('openai')
        ->generate('Unauthorized');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 401');

/**
 * @psalm-suppress UnusedVariable
 */
it('can call writer', function (): void {
    foreach ([
        '{"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "  ", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}

',
        '{"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "use", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}

',
        '{"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " App", "index": 0, "logprobs": null, "finish_reason": "length"}], "model": "text-davinci-003"}

',
        '[DONE]

',
    ] as $rowResponse) {
        (function (string $rowResponse) use (&$messages): void {
            $this->buildWriter($messages)($rowResponse);
        })->call(app(GeneratorManager::class)->driver('openai'), $rowResponse);
    }

    expect($messages)->toBe('  use App');
})->group(__DIR__, __FILE__);
