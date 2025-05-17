<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection JsonEncodingApiUsageInspection */
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

use App\Support\OpenAI;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * @psalm-suppress UnusedClosureParam
 */
beforeEach(function (): void {
    setup_http_fake();

    $this->openAI = new OpenAI(Arr::only(
        config('ai-commit.generators.openai'),
        ['http_options', 'retry', 'base_url', 'api_key']
    ));
});

/**
 * @psalm-suppress UndefinedPropertyFetch
 */
it('can sanitize data', function (): void {
    $data = 'data: {"id": "cmpl-6n1mYrlWTmE9184S4pajlIx6JITEu", "object": "text_completion", "created": 1677142942, "choices": [{"text": "", "index": 0, "logprobs": null, "finish_reason": "stop"}], "model": "text-davinci-003"}';
    expect($data)->not->toBeJson()
        ->and(OpenAI::sanitizeData($data))->toBeJson();
})->group(__DIR__, __FILE__);

it('can completions', function (): void {
    $parameters = config('ai-commit.generators.openai.parameters');
    $parameters['prompt'] = 'OK';
    $response = $this->openAI->completions($parameters, function (): void {});

    expect($response->json('choices.0.text'))->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('will throw RequestException when completions', function (): void {
    $parameters = config('ai-commit.generators.openai.parameters');
    $parameters['prompt'] = 'Too Many Requests';
    $this->openAI->completions($parameters, function (): void {});
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 429');

it('can chat completions', function (): void {
    $parameters = config('ai-commit.generators.openai_chat.parameters');
    $parameters['messages'] = [
        ['role' => 'user', 'content' => 'OK'],
    ];

    expect($this->openAI->chatCompletions($parameters, function (): void {}))->toBeInstanceOf(Response::class)
        ->body()->toBeJson();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('can models', function (): void {
    expect($this->openAI->models())->toBeInstanceOf(Response::class)
        ->body()->toBeJson();
})->group(__DIR__, __FILE__);
