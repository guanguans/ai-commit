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

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
final class OpenAI extends FoundationSDK
{
    public function completions(array $parameters, ?callable $writer = null): Response
    {
        return $this
            ->clonePendingRequest()
            ->when(
                is_callable($writer),
                static function (PendingRequest $pendingRequest) use ($writer): void {
                    $pendingRequest->withOptions([
                        'curl' => [
                            CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use ($writer): int {
                                $writer($data, $ch);

                                return strlen($data);
                            },
                        ],
                    ]);
                }
            )
            ->post('completions', validate(
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
                    'temperature' => 'numeric',
                    'top_p' => 'numeric',
                    'n' => 'integer',
                    'stream' => 'bool',
                    'logprobs' => 'nullable|integer',
                    'echo' => 'bool',
                    // 'stop' => 'nullable|string|array',
                    'stop' => 'nullable|string',
                    'presence_penalty' => 'numeric',
                    'frequency_penalty' => 'numeric',
                    'best_of' => 'integer',
                    'logit_bias' => 'array', // map
                    'user' => 'string|uuid',
                ]
            ))
            ->throw();
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
