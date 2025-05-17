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
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    setup_http_fake();
});

it('can generate commit messages', function (): void {
    expect(app(GeneratorManager::class)->driver('ernie_bot'))
        ->generate('OK')->toBeString()->not->toBeEmpty();
    // Http::assertSentCount(1);
});

/**
 * @psalm-suppress UnusedVariable
 */
it('can call writer', function (): void {
    foreach ([
        '{"id":"as-rx9g6c5sqp","object":"chat.completion","created":1692253330,"sentence_id":2,"is_end":false,"is_truncated":false,"result":"PHP的语法借鉴吸收C语言、Java和语言的特点，易于一般程序员学习。","need_clear_history":false,"usage":{"prompt_tokens":4,"completion_tokens":35,"total_tokens":87}}

',
        '{"id":"as-rx9g6c5sqp","object":"chat.completion","created":1692253331,"sentence_id":3,"is_end":false,"is_truncated":false,"result":"PHP的主要目标是允许网络开发人P也被用于其他很多领域。","need_clear_history":false,"usage":{"prompt_tokens":4,"completion_tokens":35,"total_tokens":122}}

',
    ] as $rowResponse) {
        (function (string $rowResponse) use (&$messages): void {
            $this->buildWriter($messages)($rowResponse);
        })->call(app(GeneratorManager::class)->driver('ernie_bot'), $rowResponse);
    }

    expect($messages)->toBe('PHP的语法借鉴吸收C语言、Java和语言的特点，易于一般程序员学习。PHP的主要目标是允许网络开发人P也被用于其他很多领域。');
})->group(__DIR__, __FILE__);
