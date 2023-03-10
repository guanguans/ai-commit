<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

uses(TestCase::class)
    ->beforeAll(function (): void {
    })
    ->beforeEach(function (): void {
    })
    ->afterEach(function (): void {
    })
    ->afterAll(function (): void {
    })
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * @param object|string $class
 */
function class_namespace($class): string
{
    $class = is_object($class) ? get_class($class) : $class;

    return (new ReflectionClass($class))->getNamespaceName();
}

function repository_path(string $path = ''): string
{
    return fixtures_path('repository'.($path ? DIRECTORY_SEPARATOR.$path : $path));
}

function fixtures_path(string $path = ''): string
{
    return __DIR__.'/Fixtures'.($path ? DIRECTORY_SEPARATOR.$path : $path);
}

/**
 * @psalm-suppress UnusedClosureParam
 */
function setup_http_fake(): void
{
    Http::fake([
        '*://api.openai.com/v1/completions' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['prompt'];
            $status = transform($prompt, function ($prompt) {
                return array_flip(Response::$statusTexts)[$prompt] ?? 200;
            });

            $text = transform($prompt, function ($prompt) {
                switch ($prompt) {
                    case 'empty':
                        $text = '';

                        break;
                    case 'invalid':
                        $text = '[    {        "id": 1,        "subject": "Fix(OpenAIGenerator): Debugging output",        "body": "- Add var_dump() for debugging output- Add var_dump() for stream response"    },    {        "id": 2,        "subject": "Refactor(OpenAIGenerator): Error handling",        "body": "- Check for error response in JSON- Handle error response"    },    {        "id": 3,        "subject": "Docs(OpenAIGenerator): Update documentation",        "body": "- Update documentation for OpenAIGenerator class",    }]';

                        break;
                    default:
                        $text = '[    {        "id": 1,        "subject": "Fix(OpenAIGenerator): Debugging output",        "body": "- Add var_dump() for debugging output- Add var_dump() for stream response"    },    {        "id": 2,        "subject": "Refactor(OpenAIGenerator): Error handling",        "body": "- Check for error response in JSON- Handle error response"    },    {        "id": 3,        "subject": "Docs(OpenAIGenerator): Update documentation",        "body": "- Update documentation for OpenAIGenerator class"    }]';

                        break;
                }

                return $text;
            });

            $body = $status >= 400
                ?
                [
                    'error' => [
                        'message' => 'Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.',
                        'type' => 'invalid_request_error',
                        'param' => null,
                        'code' => 'invalid_api_key',
                    ],
                ]
                :
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
                ];

            return Http::response($body, $status);
        },
        '*://api.openai.com/v1/chat/completions' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['messages'][0]['content'];
            $status = transform($prompt, function ($prompt) {
                return array_flip(Response::$statusTexts)[$prompt] ?? 200;
            });

            $body = $status >= 400
                ?
                [
                    'error' => [
                        'message' => 'Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.',
                        'type' => 'invalid_request_error',
                        'param' => null,
                        'code' => 'invalid_api_key',
                    ],
                ]
                :
                [
                    'id' => 'chatcmpl-6pqDoRwRGQAlRvJnesR9QMG9rxpyK',
                    'object' => 'chat.completion',
                    'created' => 1677813488,
                    'model' => 'gpt-3.5-turbo-0301',
                    'usage' => [
                        'prompt_tokens' => 8,
                        'completion_tokens' => 16,
                        'total_tokens' => 24,
                    ],
                    'choices' => [
                        [
                            'delta' => [
                                'role' => 'assistant',
                                'content' => 'PHP (Hypertext Preprocessor) is a server-side scripting language used',
                            ],
                            'finish_reason' => 'length',
                            'index' => 0,
                        ],
                    ],
                ];

            return Http::response($body, $status);
        },

        '*://api.openai.com/v1/models' => function (Request $request, array $options): PromiseInterface {
            return Http::response(
                <<<'body'
{
    "object": "list",
    "data": [
        {
            "id": "babbage",
            "object": "model",
            "created": 1649358449,
            "owned_by": "openai",
            "permission": [
                {
                    "id": "modelperm-49FUp5v084tBB49tC4z8LPH5",
                    "object": "model_permission",
                    "created": 1669085501,
                    "allow_create_engine": false,
                    "allow_sampling": true,
                    "allow_logprobs": true,
                    "allow_search_indices": false,
                    "allow_view": true,
                    "allow_fine_tuning": false,
                    "organization": "*",
                    "group": null,
                    "is_blocking": false
                }
            ],
            "root": "babbage",
            "parent": null
        }
    ]
}
body
            );
        },
    ]);
}

function reset_http_fake(?Factory $factory = null): void
{
    (function (): void {
        $this->stubCallbacks = collect();
    })->call($factory ?? Http::getFacadeRoot());
}
