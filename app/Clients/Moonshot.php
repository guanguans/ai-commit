<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Clients;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

/**
 * @see https://platform.moonshot.cn/docs/api-reference
 */
final class Moonshot extends AbstractClient
{
    /**
     * ```ok
     * {
     *     'id': 'chatcmpl-6pqDoRwRGQAlRvJnesR9QMG9rxpyK',
     *     'object': 'chat.completion',
     *     'created': 1677813488,
     *     'model': 'gpt-3.5-turbo-0301',
     *     'usage': {
     *     'prompt_tokens': 8,
     *         'completion_tokens': 16,
     *         'total_tokens': 24
     *     },
     *     'choices': [
     *         {
     *             'delta': {
     *             'role': 'assistant',
     *                 'content': 'PHP (Hypertext Preprocessor) is a server-side scripting language used'
     *             },
     *             'finish_reason': 'length',
     *             'index': 0
     *         }
     *     ]
     * }
     * ```.
     *
     * ```stream ok
     * data: {"id":"chatcmpl-6pqQB0NVBCjNcs6aPeFUi4gy1pCoj","object":"chat.completion.chunk","created":1677814255,"model":"gpt-3.5-turbo-0301","choices":[{"delta":{"content":" used"},"index":0,"finish_reason":null}]}
     *
     * data: {"id":"chatcmpl-6pqQB0NVBCjNcs6aPeFUi4gy1pCoj","object":"chat.completion.chunk","created":1677814255,"model":"gpt-3.5-turbo-0301","choices":[{"delta":{},"index":0,"finish_reason":"length"}]}
     *
     * data: [DONE]
     * ```
     *
     * @throws BindingResolutionException
     * @throws RequestException
     */
    public function chatCompletions(array $parameters, ?callable $writer = null): Response
    {
        $response = $this
            ->when(
                ($parameters['stream'] ?? false) && \is_callable($writer),
                static function (PendingRequest $pendingRequest) use (&$rowData, $writer): PendingRequest {
                    return $pendingRequest->withOptions([
                        'curl' => [
                            \CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use (&$rowData, $writer): int {
                                // $sanitizeData = self::sanitizeData($data);
                                // if (! str($data)->startsWith('data: [DONE]')) {
                                //     $rowData = $sanitizeData;
                                // }

                                $rowData .= $data;

                                $writer($data, $ch);

                                return \strlen($data);
                            },
                        ],
                    ]);
                }
            )
            ->post('chat/completions', $this->validate($parameters, [
                'model' => [
                    'required',
                    'string',
                    // 'in:moonshot-v1-8k,moonshot-v1-32k,moonshot-v1-128k',
                ],
                'messages' => 'required|array',
                'temperature' => 'numeric|between:0,2',
                'top_p' => 'numeric|between:0,1',
                'n' => 'integer|min:1',
                'stream' => 'bool',
                // 'stop' => 'nullable|string|array',
                'stop' => 'nullable|string',
                'max_tokens' => 'integer',
                'presence_penalty' => 'numeric|between:-2,2',
                'frequency_penalty' => 'numeric|between:-2,2',
                // 'logit_bias' => 'array', // map
                // 'user' => 'string|uuid',
            ]));

        if ($rowData && empty($response->body())) {
            $response = new Response($response->toPsrResponse()->withBody(Utils::streamFor($rowData)));
        }

        return $response->throw();
    }

    /**
     * @throws RequestException
     */
    public function models(): Response
    {
        return $this->get('models')->throw();
    }

    protected function configRules(): array
    {
        return [
            'api_key' => 'required|string',
        ];
    }

    protected function extendPendingRequest(PendingRequest $pendingRequest): PendingRequest
    {
        return $pendingRequest
            ->baseUrl($this->configRepository->get('base_url', 'https://api.moonshot.cn/v1'))
            ->withToken($this->configRepository->get('api_key'));
    }
}
