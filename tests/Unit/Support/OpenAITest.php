<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Support\OpenAI;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * @psalm-suppress UnusedClosureParam
 */
beforeEach(function () {
    $this->openAI = new OpenAI(Arr::only(
        config('ai-commit.generators.openai'),
        ['http_options', 'retry', 'base_url', 'api_key']
    ));
});

it('can completions', function () {
    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'OK';
    $response = $this->openAI->completions($parameters, function () {});

    expect($response->json('choices.0.text'))->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('will throw RequestException when completions', function () {
    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'Too Many Requests';
    $this->openAI->completions($parameters, function () {});
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 429');
