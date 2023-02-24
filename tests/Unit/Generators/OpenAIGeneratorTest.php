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
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress UnusedClosureParam
 */
it('can generate commit messages', function () {
    Http::fake([
        '*://api.openai.com/v1/*' => function (Request $request, array $options) use (&$text) {
            $text = '[    {        "id": 1,        "subject": "Fix(OpenAIGenerator): Debugging output",        "body": "- Add var_dump() for debugging output- Add var_dump() for stream response"    },    {        "id": 2,        "subject": "Refactor(OpenAIGenerator): Error handling",        "body": "- Check for error response in JSON- Handle error response"    },    {        "id": 3,        "subject": "Docs(OpenAIGenerator): Update documentation",        "body": "- Update documentation for OpenAIGenerator class"    }]';

            return Http::response(
                [
                    'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
                    'object' => 'text_completion',
                    'created' => 1677143178,
                    'model' => 'text-davinci-003',
                    'choices' => [
                        0 => [
                            'text' => $text,
                            'index' => 0,
                            'logprobs' => null,
                            'finish_reason' => 'stop',
                        ],
                    ],
                    'usage' => [
                        'prompt_tokens' => 749,
                        'completion_tokens' => 159,
                        'total_tokens' => 908,
                    ],
                ],
                transform($options['laravel_data']['prompt'], function ($prompt) {
                    return array_flip(Response::$statusTexts)[$prompt] ?? 200;
                })
            );
        },
    ]);

    expect(app(GeneratorManager::class)->driver('openai'))
        ->generate('OK')->toBe($text)->toBeString();
    Http::assertSentCount(1);
});
