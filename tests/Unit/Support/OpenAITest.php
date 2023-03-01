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
it('can hydrate data', function (): void {
    $data = 'data: {"id": "cmpl-6n1mYrlWTmE9184S4pajlIx6JITEu", "object": "text_completion", "created": 1677142942, "choices": [{"text": "", "index": 0, "logprobs": null, "finish_reason": "stop"}], "model": "text-davinci-003"}';
    expect($data)->not->toBeJson()
        ->and(OpenAI::hydrateData($data))->toBeJson();
})->group(__DIR__, __FILE__);

it('can completions', function (): void {
    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'OK';
    $response = $this->openAI->completions($parameters, function (): void {});

    expect($response->json('choices.0.text'))->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

/**
 * @psalm-suppress UnusedVariable
 */
it('can completions stream response ', function (): void {
    reset_http_fake();
    Http::fake(function () {
        $body = <<<body
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "names", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "pace", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " App", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\\", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "Http", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\\", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "Cont", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "rollers", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": ";", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "use", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " Illum", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "inate", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\\", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "Http", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\\", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "Request", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": ";", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "use", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " App", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\\", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "User", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": ";", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "use", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}\n
\n
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " App", "index": 0, "logprobs": null, "finish_reason": "length"}], "model": "text-davinci-003"}\n
\n
data: [DONE]\n
\n
body;
        $body = <<<body
data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "\n", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}

data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": "use", "index": 0, "logprobs": null, "finish_reason": null}], "model": "text-davinci-003"}

data: {"id": "cmpl-6or3mHmSgvCePOlM34DK90rm6J0ec", "object": "text_completion", "created": 1677578382, "choices": [{"text": " App", "index": 0, "logprobs": null, "finish_reason": "length"}], "model": "text-davinci-003"}

data: [DONE]

body;

        return Http::response(str_remove_cntrl($body));
    });

    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'OK';
    $parameters['stream'] = true;
    $response = $this->openAI->completions($parameters);

    expect($response->json('choices.0.text'))->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__)->skip();

it('will throw RequestException when completions', function (): void {
    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'Too Many Requests';
    $this->openAI->completions($parameters, function (): void {});
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 429');
