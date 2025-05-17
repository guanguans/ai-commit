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
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\GeneratorManager;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    setup_http_fake();
});

it('can generate commit messages', function (): void {
    config('ai-commit')->set('generators.moonshot.parameters.stream', false);
    expect(app(GeneratorManager::class)->driver('moonshot'))
        ->generate('OK')->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('will throw forbidden RequestException', function (): void {
    app(GeneratorManager::class)->driver('moonshot')->generate('Forbidden');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 403');

it('will throw unauthorized RequestException', function (): void {
    app(GeneratorManager::class)
        ->tap(function (): void {
            // reset_http_fake();
        })
        ->driver('moonshot')
        ->generate('Unauthorized');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 401');

/**
 * @psalm-suppress UnusedVariable
 */
it('can call writer', function (): void {
    foreach ([
        <<<'EOD'
            {"id":"cmpl-bacb10e1f94a491eb55064b467587065","object":"chat.completion.chunk","created":4114970,"model":"moonshot-v1-8k","choices":[{"index":0,"delta":{"content":" to"},"finish_reason":null}]}


            EOD,
        <<<'EOD'
            {"id":"cmpl-bacb10e1f94a491eb55064b467587065","object":"chat.completion.chunk","created":4114970,"model":"moonshot-v1-8k","choices":[{"index":0,"delta":{"content":" display"},"finish_reason":null}]}


            EOD,
        <<<'EOD'
            {"id":"cmpl-bacb10e1f94a491eb55064b467587065","object":"chat.completion.chunk","created":4114970,"model":"moonshot-v1-8k","choices":[{"index":0,"delta":{},"finish_reason":"stop","usage":{"prompt_tokens":492,"completion_tokens":49,"total_tokens":541}}]}


            EOD,
        <<<'EOD'
            [DONE]


            EOD,
    ] as $rowResponse) {
        (function (string $rowResponse) use (&$messages): void {
            $this->buildWriter($messages)($rowResponse);
        })->call(app(GeneratorManager::class)->driver('moonshot'), $rowResponse);
    }

    expect($messages)->toBe(' to display');
})->group(__DIR__, __FILE__);
