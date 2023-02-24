<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\GeneratorManager;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    setup_http_fake();
});

it('can generate commit messages', function () {
    expect(app(GeneratorManager::class)->driver('openai'))
        ->generate('OK')->toBeString()->not->toBeEmpty();
    Http::assertSentCount(1);
});

it('will throw forbidden RequestException', function () {
    app(GeneratorManager::class)->driver('openai')->generate('Forbidden');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 403');

it('will throw unauthorized RequestException', function () {
    app(GeneratorManager::class)
        ->tap(function () {
            // reset_http_fake();
        })
        ->driver('openai')
        ->generate('Unauthorized');
})->group(__DIR__, __FILE__)->throws(RequestException::class, 'HTTP request returned status code 401');
