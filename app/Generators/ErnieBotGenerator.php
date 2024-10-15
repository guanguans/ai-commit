<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Generators;

use App\Support\Ernie;
use ArrayAccess;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ErnieBotGenerator extends Generator
{
    /**
     * @var \App\Support\Ernie
     */
    protected $ernie;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->ernie = new Ernie(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key', 'secret_key']));
    }

    /**
     * @psalm-suppress RedundantCast
     * @psalm-suppress UnusedVariable
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @noinspection PhpCastIsUnnecessaryInspection
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'user_id' => Str::uuid()->toString(),
        ] + Arr::get($this->config, 'parameters', []);

        $response = $this->completion($parameters, $this->buildWriter($messages));

        // fake 响应
        return (string) ($messages ?? $this->getCompletionMessages($response));
    }

    /**
     * @throws RequestException
     * @throws BindingResolutionException
     */
    protected function completion(array $parameters, ?callable $writer = null): Response
    {
        return $this->ernie->ernieBot($parameters, $writer);
    }

    /**
     * @noinspection JsonEncodingApiUsageInspection
     */
    private function buildWriter(?string &$messages): \Closure
    {
        return function (string $data) use (&$messages): void {
            // (正常|错误|流)响应
            $rowResponse = (array) json_decode($data, true);
            $messages .= $text = $this->getCompletionMessages($rowResponse);
            $this->output->write($text);
        };
    }

    /**
     * @param array|ArrayAccess $response
     */
    private function getCompletionMessages($response): string
    {
        return Arr::get($response, 'result', '');
    }
}
