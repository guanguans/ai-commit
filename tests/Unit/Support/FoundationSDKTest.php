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
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

beforeEach(function (): void {
    $this->openAI = new OpenAI(Arr::only(
        config('ai-commit.generators.openai'),
        ['http_options', 'retry', 'base_url', 'api_key']
    ));
});

it('can dd request data', function (): void {
    expect($this->openAI)
        ->ddRequestData()->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can dump request data', function (): void {
    expect($this->openAI)
        ->dumpRequestData()->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can build log middleware', function (): void {
    expect($this->openAI)
        ->buildLogMiddleware($this->app->get(LoggerInterface::class))->toBeCallable();
})->group(__DIR__, __FILE__);

it('can clone pending request', function (): void {
    expect($this->openAI)
        ->cloneDefaultPendingRequest()->toBeInstanceOf(PendingRequest::class);
})->group(__DIR__, __FILE__);
