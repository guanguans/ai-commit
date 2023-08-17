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

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Stringable;

/**
 * @see https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu
 */
final class Ernie extends FoundationSDK
{
    /**
     * @var null|string
     */
    private static $accessToken;

    public static function sanitizeData(string $data): string
    {
        return (string) str($data)->whenStartsWith(
            $prefix = 'data: ',
            static function (Stringable $data) use ($prefix): Stringable {
                return $data->replaceFirst($prefix, '');
            }
        );
    }

    /**
     * ```json
     * {
     *     'id': 'as-rkmymnxvrx',
     *     'object': 'chat.completion',
     *     'created': 1692254707,
     *     'sentence_id': 2,
     *     'is_end': false,
     *     'is_truncated': false,
     *     'result': 'PHP最初是作为HTML辅助程序开发网页。',
     *     'need_clear_history': false,
     *     'usage': {
     *     'prompt_tokens': 4,
     *         'completion_tokens': 25,
     *         'total_tokens': 84
     *     }
     * }
     * ```.
     *
     * ```stream
     * data: {'id':'as-rx9g6c5sqp','object':'chat.completion','created':1692253330,'sentence_id':2,'is_end':false,'is_truncated':false,'result':'PHP的语法借鉴吸收C语言、Java和语言的特点，易于一般程序员学习。','need_clear_history':false,'usage':{'prompt_tokens':4,'completion_tokens':35,'total_tokens':87}}
     *
     * data: {'id':'as-rx9g6c5sqp','object':'chat.completion','created':1692253331,'sentence_id':3,'is_end':false,'is_truncated':false,'result':'PHP的主要目标是允许网络开发人P也被用于其他很多领域。','need_clear_history':false,'usage':{'prompt_tokens':4,'completion_tokens':35,'total_tokens':122}}
     * ```
     *
     * ```error
     * {'error_code':17,'error_msg':'Open api daily request limit reached'}
     * ```
     *
     * @throws BindingResolutionException
     * @throws RequestException
     */
    public function ernieBot(array $parameters, ?callable $writer = null): Response
    {
        return $this->completion('rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions', $parameters, $writer);
    }

    /**
     * @throws RequestException
     * @throws BindingResolutionException
     */
    public function ernieBotTurbo(array $parameters, ?callable $writer = null): Response
    {
        return $this->completion('rpc/2.0/ai_custom/v1/wenxinworkshop/chat/eb-instant', $parameters, $writer);
    }

    /**
     * @throws RequestException
     */
    public function oauthToken(): Response
    {
        return $this->cloneDefaultPendingRequest()
            ->get(
                'oauth/2.0/token',
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config['api_key'],
                    'client_secret' => $this->config['secret_key'],
                ]
            )
            ->throw();
    }

    /**
     * @throws RequestException
     */
    protected function getAccessToken(): string
    {
        return self::$accessToken ?? self::$accessToken = $this->oauthToken()->json('access_token');
    }

    /**
     * {@inheritDoc}
     *
     * @throws BindingResolutionException
     */
    protected function validateConfig(array $config): array
    {
        return array_replace_recursive(
            [
                'http_options' => [
                    // \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 30,
                    // \GuzzleHttp\RequestOptions::TIMEOUT => 180,
                ],
                'retry' => [
                    // 'times' => 1,
                    // 'sleep' => 1000,
                    // 'when' => static function (\Exception $exception): bool {
                    //     return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                    // },
                    // // 'throw' => true,
                ],
                'base_url' => 'https://aip.baidubce.com',
            ],
            validate($config, [
                'http_options' => 'array',
                'retry' => 'array',
                'retry.times' => 'integer',
                'retry.sleep' => 'integer',
                'retry.when' => 'nullable',
                // 'retry.throw' => 'bool',
                'base_url' => 'string',
                'api_key' => 'required|string',
                'secret_key' => 'required|string',
            ])
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function buildDefaultPendingRequest(array $config): PendingRequest
    {
        return parent::buildDefaultPendingRequest($config)
            ->baseUrl($config['base_url'])
            ->asJson()
            // ->dump()
            // ->throw()
            // ->retry(
            //     $config['retry']['times'],
            //     $config['retry']['sleep'],
            //     $config['retry']['when']
            //     // $config['retry']['throw']
            // )
            ->withOptions($config['http_options']);
    }

    /**
     * @psalm-suppress UnusedVariable
     * @psalm-suppress UnevaluatedCode
     *
     * @throws RequestException
     * @throws BindingResolutionException
     */
    private function completion(
        string $url,
        array $parameters,
        ?callable $writer = null,
        array $messages = [],
        array $customAttributes = []
    ): Response {
        $response = $this
            ->cloneDefaultPendingRequest()
            ->when(
                ($parameters['stream'] ?? false) && \is_callable($writer),
                static function (PendingRequest $pendingRequest) use (&$rowData, $writer): PendingRequest {
                    return $pendingRequest->withOptions([
                        'curl' => [
                            CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use (&$rowData, $writer): int {
                                $rowData = $data;
                                $writer($data, $ch);

                                return \strlen($data);
                            },
                        ],
                    ]);
                }
            )
            ->withOptions(['query' => [
                'access_token' => $this->getAccessToken(),
            ]])
            ->post($url, validate(
                $parameters,
                [
                    'messages' => 'required|array',
                    'temperature' => 'numeric|between:0,1',
                    'top_p' => 'numeric|between:0,1',
                    'penalty_score' => 'numeric|between:1,2',
                    'stream' => 'bool',
                    'user_id' => 'string',
                ],
                $messages,
                $customAttributes
            ));

        if ($rowData && empty($response->body())) {
            $response = new Response(
                $response->toPsrResponse()->withBody(Utils::streamFor(self::sanitizeData($rowData)))
            );
        }

        return $response->throw();
    }
}
