<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection SqlResolve */
declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\Clients\Ernie;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * @psalm-suppress UnusedClosureParam
 */
beforeEach(function (): void {
    setup_http_fake();

    $this->ernie = new Ernie(Arr::only(
        config('ai-commit.generators.ernie_bot'),
        ['http_options', 'retry', 'base_url', 'api_key', 'secret_key']
    ));
});

it('can ernieBot completion', function (): void {
    $parameters = config('ai-commit.generators.ernie_bot.parameters');
    $parameters['messages'] = [
        ['role' => 'user', 'content' => 'OK'],
    ];
    $response = $this->ernie->ernieBot($parameters, function (): void {});

    expect($response->json('result'))->toBeString()->not->toBeEmpty();
    // Http::assertSentCount(2);
})->group(__DIR__, __FILE__);

it('will throw RequestException when ernieBot completion', function (): void {
    $parameters = config('ai-commit.generators.ernie_bot.parameters');
    $parameters['messages'] = [
        ['role' => 'user', 'content' => 'Too Many Requests'],
    ];
    $this->ernie->ernieBot($parameters, function (): void {});
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'Open api daily request limit reached');

it('can ernieBotTurbo completion', function (): void {
    $parameters = config('ai-commit.generators.ernie_bot_turbo.parameters');
    $parameters['messages'] = [
        ['role' => 'user', 'content' => 'OK'],
    ];

    expect($this->ernie->ernieBotTurbo($parameters, function (): void {}))->toBeInstanceOf(Response::class)
        ->body()->toBeJson();
    // Http::assertSentCount(1);
})->group(__DIR__, __FILE__);
