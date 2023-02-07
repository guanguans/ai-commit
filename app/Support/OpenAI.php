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

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
class OpenAI extends FoundationSDK
{
    public function completions(array $parameters, ?callable $writer = null): Response
    {
        $pendingRequest = $this->clonePendingRequest();

        if ($writer) {
            $pendingRequest->withOptions([
                'curl' => [
                    CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use ($writer) {
                        $writer($data, $ch);

                        return strlen($data);
                    },
                ],
            ]);
        }

        return $pendingRequest->post('completions', validate(
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
        return array_merge(
            [
                'http_options' => [],
                'base_url' => 'https://api.openai.com/v1',
                'retry' => [
                    'times' => 1,
                    'sleepMilliseconds' => 100,
                    // 'when' => static function (\Throwable $throwable) {
                    //     return $throwable instanceof ConnectionException;
                    // },
                    // 'throw' => true,
                ],
            ],
            validate($config, [
                'http_options' => 'array',
                'base_url' => 'string',
                'api_key' => 'required|string',
                'retry' => 'array',
                'retry.times' => 'integer',
                'retry.sleepMilliseconds' => 'integer',
                // 'retry.when' => 'nullable',
                // 'retry.throw' => 'bool',
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
                $config['retry']['sleepMilliseconds']
                // $config['retry']['when'],
                // $config['retry']['throw']
            );
    }
}
