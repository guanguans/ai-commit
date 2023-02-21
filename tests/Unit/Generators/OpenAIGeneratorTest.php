<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Generators\OpenAIGenerator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

/**
 * @psalm-suppress UnusedClosureParam
 */
it('can generate commit messages', function () {
    Http::fake(function (Request $request, array $options) {
        return Http::response('foo');
    });

    $openAIGenerator = new OpenAIGenerator(config('ai-commit.generators.openai'));
    $generate = $openAIGenerator->generate('foo');
    expect($generate)->toBeString();
    Http::assertSentCount(1);
});
