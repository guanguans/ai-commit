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

use App\Support\Moonshot;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * @psalm-suppress UnusedClosureParam
 */
beforeEach(function (): void {
    setup_http_fake();

    $this->moonshot = new Moonshot(Arr::only(
        config('ai-commit.generators.moonshot'),
        ['http_options', 'retry', 'base_url', 'api_key']
    ));
});

/**
 * @psalm-suppress UndefinedPropertyFetch
 */
it('can sanitize data', function (): void {
    $data = 'data: {"id": "cmpl-6n1mYrlWTmE9184S4pajlIx6JITEu", "object": "text_completion", "created": 1677142942, "choices": [{"text": "", "index": 0, "logprobs": null, "finish_reason": "stop"}], "model": "text-davinci-003"}';
    expect($data)->not->toBeJson()
        ->and(Moonshot::sanitizeData($data))->toBeJson();
})->group(__DIR__, __FILE__);

it('can chat completions', function (): void {
    $parameters = config('ai-commit.generators.moonshot.parameters');
    $parameters['messages'] = [
        ['role' => 'user', 'content' => 'OK'],
    ];

    expect($this->moonshot->chatCompletions($parameters, function (): void {}))->toBeInstanceOf(Response::class)
        ->body()->toBeJson();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('can models', function (): void {
    expect($this->moonshot->models())->toBeInstanceOf(Response::class)
        ->body()->toBeJson();
})->group(__DIR__, __FILE__);
