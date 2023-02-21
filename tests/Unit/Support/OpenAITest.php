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
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

/**
 * @psalm-suppress UnusedClosureParam
 */
beforeEach(function () {
    $this->openAI = new OpenAI(Arr::only(
        config('ai-commit.generators.openai'),
        ['http_options', 'retry', 'base_url', 'api_key']
    ));

    Http::fake(function (Request $request, array $options) {
        return Http::response('foo');
    });
});

it('can dd request data', function () {
    expect($this->openAI)
        ->ddRequestData()
        ->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can dump request data', function () {
    expect($this->openAI)
        ->dumpRequestData()
        ->toBeInstanceOf(OpenAI::class);

    Http::get('foo');
    Http::assertSentCount(1);
})->group(__DIR__, __FILE__);

it('can dump data', function () {
    /** @noinspection DebugFunctionUsageInspection */
    expect($this->openAI)
        ->dump()
        ->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can with log middleware', function () {
    expect($this->openAI)
        ->withLogMiddleware($this->app->get(LoggerInterface::class))
        ->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can clone pending request', function () {
    expect($this->openAI)
        ->clonePendingRequest()
        ->toBeInstanceOf(PendingRequest::class);
})->group(__DIR__, __FILE__);

/**
 * @psalm-suppress UnusedVariable
 * @psalm-suppress UnusedClosureParam
 */
it('can completions', function () {
    Http::fake(function (Request $request, array $options) {
        return Http::response('foo');
    });

    $parameters = config('ai-commit.generators.openai.completion_parameters');
    $parameters['prompt'] = 'prompt';

    $this->openAI->completions($parameters, function (string $data) use (&$messages): void {
        if (\str($data)->isJson()) {
            // 错误响应
            $response = json_decode($data, true);
            if (isset($response['error']['message'])) {
                return;
            }

            // 正常响应
            $text = Arr::get($response, 'choices.0.text', '');
            $messages .= $text;

            return;
        }

        // 流响应
        $data = \str($data)->replaceFirst('data: ', '')->rtrim();
        if ($data->startsWith('[DONE]')) {
            return;
        }
        $text = Arr::get(json_decode((string) $data, true), 'choices.0.text', '');
        $messages .= $text;
    });

    Http::assertSentCount(1);
    expect($messages)->toBeString();
})->group(__DIR__, __FILE__)->skip();
