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

use App\Contracts\GeneratorContract;
use App\Support\Ernie;
use ArrayAccess;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ErnieBotGenerator implements GeneratorContract
{
    /**
     * @var \App\Support\Ernie
     */
    protected $ernie;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    private $outputStyle;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->ernie = new Ernie(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key', 'secret_key']));
        $this->outputStyle = resolve(OutputStyle::class);
    }

    /**
     * @psalm-suppress RedundantCast
     * @psalm-suppress UnusedVariable
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'user_id' => Str::uuid()->toString(),
        ] + Arr::get($this->config, 'parameters', []);

        $response = $this->completion($parameters, function (string $data) use (&$messages): void {
            // (正常|错误|流)响应
            $rowResponse = (array) json_decode(Ernie::sanitizeData($data), true, 512, JSON_THROW_ON_ERROR);
            $messages .= $text = $this->getCompletionMessages($rowResponse);
            $this->outputStyle->write($text);
        });

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
     * @param array|ArrayAccess $response
     */
    private function getCompletionMessages($response): string
    {
        return Arr::get($response, 'result', '');
    }
}
