<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Stringable;
use Psr\Http\Message\ResponseInterface;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
final class OpenAI extends FoundationSDK
{
    public static function hydrateData(string $data): string
    {
        return (string) \str($data)->whenStartsWith($prefix = 'data: ', static function (Stringable $data) use ($prefix) {
            return $data->replaceFirst($prefix, '');
        });
    }

    /**
     * @psalm-suppress UnusedVariable
     * @psalm-suppress UnevaluatedCode
     *
     * ```ok
     * {
     *     "id": "cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB",
     *     "object": "text_completion",
     *     "created": 1677143178,
     *     "model": "text-davinci-003",
     *     "choices": [
     *         {
     *             "text": "\n\n[\n    {\n        \"id\": 1,\n        \"subject\": \"Fix(OpenAIGenerator): Debugging output\",\n        \"body\": \"- Add var_dump() for debugging output\\n- Add var_dump() for stream response\"\n    },\n    {\n        \"id\": 2,\n        \"subject\": \"Refactor(OpenAIGenerator): Error handling\",\n        \"body\": \"- Check for error response in JSON\\n- Handle error response\"\n    },\n    {\n        \"id\": 3,\n        \"subject\": \"Docs(OpenAIGenerator): Update documentation\",\n        \"body\": \"- Update documentation for OpenAIGenerator class\"\n    }\n]",
     *             "index": 0,
     *             "logprobs": null,
     *             "finish_reason": "stop"
     *         }
     *     ],
     *     "usage": {
     *         "prompt_tokens": 749,
     *         "completion_tokens": 159,
     *         "total_tokens": 908
     *     }
     * }
     * ```.
     *
     * ```stream ok
     * data: {"id": "cmpl-6n1mYrlWTmE9184S4pajlIx6JITEu", "object": "text_completion", "created": 1677142942, "choices": [{"text": "", "index": 0, "logprobs": null, "finish_reason": "stop"}], "model": "text-davinci-003"}
     *
     * data: [DONE]
     *
     * ```
     *
     * ```error
     * {
     *     "error": {
     *         "message": "Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.",
     *         "type": "invalid_request_error",
     *         "param": null,
     *         "code": "invalid_api_key"
     *     }
     * }
     * ```
     */
    public function completions(array $parameters, ?callable $writer = null): Response
    {
        $response = $this
            ->clonePendingRequest()
            ->when(
                ($parameters['stream'] ?? false) && is_callable($writer),
                static function (PendingRequest $pendingRequest) use ($writer, &$rowData): PendingRequest {
                    return $pendingRequest->withOptions([
                        'curl' => [
                            CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use ($writer, &$rowData): int {
                                if (! str($data)->startsWith('data: [DONE]')) {
                                    $rowData = $data;
                                }

                                $writer($data, $ch);

                                return strlen($data);
                            },
                        ],
                    ]);
                }
            )
            // ->withMiddleware(
            //     Middleware::mapResponse(static function (ResponseInterface $response) use ($rowData) {
            //         if (empty($rowData)) {
            //             return $response;
            //         }
            //
            //         return $response->withBody(Utils::streamFor($rowData));
            //     })
            // )
            ->post(
                'completions',
                validate(
                    $parameters,
                    [
                        'model' => [
                            'required',
                            'string',
                            'in:text-davinci-003,text-curie-001,text-babbage-001,text-ada-001,text-embedding-ada-002,code-davinci-002,code-cushman-001,content-filter-alpha',
                        ],
                        // 'prompt' => 'string|array',
                        'prompt' => 'string',
                        'suffix' => 'nullable|string',
                        'max_tokens' => 'integer',
                        'temperature' => 'numeric|between:0,2',
                        'top_p' => 'numeric|between:0,1',
                        'n' => 'integer|min:1',
                        'stream' => 'bool',
                        'logprobs' => 'nullable|integer|between:0,5',
                        'echo' => 'bool',
                        // 'stop' => 'nullable|string|array',
                        'stop' => 'nullable|string',
                        'presence_penalty' => 'numeric|between:-2,2',
                        'frequency_penalty' => 'numeric|between:-2,2',
                        'best_of' => 'integer|min:1',
                        'logit_bias' => 'array', // map
                        'user' => 'string|uuid',
                    ]
                )
            );

        if ($rowData || empty($response->body())) {
            $response = new Response(
                $response->toPsrResponse()->withBody(Utils::streamFor(self::hydrateData($rowData)))
            );
        }

        return $response->throw();
    }

    /**
     * {@inheritDoc}
     */
    protected function validateConfig(array $config): array
    {
        return array_replace_recursive(
            [
                'http_options' => [
                    \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 30,
                    \GuzzleHttp\RequestOptions::TIMEOUT => 180,
                ],
                'retry' => [
                    'times' => 1,
                    'sleepMilliseconds' => 1000,
                    'when' => static function (\Throwable $throwable): bool {
                        return $throwable instanceof \Illuminate\Http\Client\ConnectionException;
                    },
                    // 'throw' => true,
                ],
                'base_url' => 'https://api.openai.com/v1',
            ],
            validate($config, [
                'http_options' => 'array',
                'retry' => 'array',
                'retry.times' => 'integer',
                'retry.sleepMilliseconds' => 'integer',
                'retry.when' => 'nullable',
                // 'retry.throw' => 'bool',
                'base_url' => 'string',
                'api_key' => 'required|string',
            ])
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function buildPendingRequest(array $config): PendingRequest
    {
        return Http::baseUrl($config['base_url'])
            // ->throw()
            ->asJson()
            ->withToken($config['api_key'])
            ->withOptions($config['http_options'])
            ->retry(
                $config['retry']['times'],
                $config['retry']['sleepMilliseconds'],
                $config['retry']['when']
                // $config['retry']['throw']
            );
    }
}
