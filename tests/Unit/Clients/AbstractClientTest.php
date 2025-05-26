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

use App\Clients\OpenAI;
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
        ->ddPendingRequest()->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can dump request data', function (): void {
    expect($this->openAI)
        ->dumpPendingRequest()->toBeInstanceOf(OpenAI::class);
})->group(__DIR__, __FILE__);

it('can build log middleware', function (): void {
    expect((fn () => $this->makeLoggerMiddleware(resolve(LoggerInterface::class)))->call($this->openAI))
        ->toBeCallable();
})->group(__DIR__, __FILE__)->skip();

it('can clone pending request', function (): void {
    expect($this->openAI)
        ->clonePendingRequest()->toBeInstanceOf(PendingRequest::class);
})->group(__DIR__, __FILE__);
