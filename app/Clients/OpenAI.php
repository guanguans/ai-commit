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

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
final class OpenAI extends AbstractClient
{
    /**
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
     * ```
     *
     * ```error|stream error
     * {
     *     "error": {
     *         "message": "Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.",
     *         "type": "invalid_request_error",
     *         "param": null,
     *         "code": "invalid_api_key"
     *     }
     * }
     * ```
     *
     * @throws BindingResolutionException
     * @throws RequestException
     */
    public function completions(array $parameters, ?callable $writer = null): Response
    {
        return $this->completion(
            'completions',
            $parameters,
            [
                'model' => [
                    'required',
                    'string',
                    // 'in:text-davinci-003,text-davinci-002,text-curie-001,text-babbage-001,text-ada-001',
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
            ],
            $writer
        );
    }

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
        return $this->completion(
            'chat/completions',
            $parameters,
            [
                'model' => [
                    'required',
                    'string',
                    // 'in:gpt-4,gpt-4-0613,gpt-4-32k,gpt-4-32k-0613,gpt-3.5-turbo,gpt-3.5-turbo-0613,gpt-3.5-turbo-16k,gpt-3.5-turbo-16k-0613',
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
                'logit_bias' => 'array', // map
                'user' => 'string|uuid',
            ],
            $writer
        );
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
            ->baseUrl($this->configRepository->get('base_url', 'https://api.openai.com/v1'))
            ->withToken($this->configRepository->get('api_key'));
    }

    /**
     * @throws BindingResolutionException
     * @throws RequestException
     */
    private function completion(string $url, array $parameters, array $rules, ?callable $writer = null, array $messages = [], array $customAttributes = []): Response
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
            // ->withMiddleware(
            //     Middleware::mapResponse(static function (ResponseInterface $response): ResponseInterface {
            //         $contents = $response->getBody()->getContents();
            //
            //         // $parameters['stream'] === true && $writer === null
            //         if ($contents && ! \str($contents)->isJson()) {
            //             $data = \str($contents)
            //                 ->explode("\n\n")
            //                 ->reverse()
            //                 ->skip(2)
            //                 ->reverse()
            //                 ->map(static function (string $rowData): array {
            //                     return json_decode(self::sanitizeData($rowData), true);
            //                 })
            //                 ->reduce(static function (array $data, array $rowData): array {
            //                     if (empty($data)) {
            //                         return $rowData;
            //                     }
            //
            //                     foreach ($data['choices'] as $index => $choice) {
            //                         $data['choices'][$index]['text'] .= $rowData['choices'][$index]['text'];
            //                     }
            //
            //                     return $data;
            //                 }, []);
            //
            //             return $response->withBody(Utils::streamFor(json_encode($data)));
            //         }
            //
            //         return $response;
            //     })
            // )
            ->post($url, $this->validate($parameters, $rules, $messages, $customAttributes));
        // ->onError(function (Response $response) use ($rowData) {
        //     if ($rowData && empty($response->body())) {
        //         (function (Response $response) use ($rowData): void {
        //             $this->response = $response->toPsrResponse()->withBody(
        //                 Utils::streamFor(OpenAI::sanitizeData($rowData))
        //             );
        //         })->call($response, $response);
        //     }
        // })

        if ($rowData && empty($response->body())) {
            $response = new Response($response->toPsrResponse()->withBody(Utils::streamFor($rowData)));
        }

        return $response->throw();
    }
}
