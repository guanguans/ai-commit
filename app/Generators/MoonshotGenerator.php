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

use App\Support\FoundationSDK;
use App\Support\Moonshot;
use Illuminate\Support\Arr;

final class MoonshotGenerator extends Generator
{
    /**
     * @var \App\Support\Moonshot
     */
    private $moonshot;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->moonshot = new Moonshot(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
    }

    /**
     * @psalm-suppress RedundantCast
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @noinspection PhpCastIsUnnecessaryInspection
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ] + Arr::get($this->config, 'parameters', []);

        $response = $this->moonshot->chatCompletions($parameters, $this->buildWriter($messages));

        // fake 响应
        return (string) ($messages ?? $this->getCompletionMessages($response));
    }

    private function getCompletionMessages($response): string
    {
        return Arr::get($this->config, 'parameters.stream', false)
            ? Arr::get($response, 'choices.0.delta.content', '')
            : Arr::get($response, 'choices.0.message.content', '');
    }

    /**
     * @noinspection JsonEncodingApiUsageInspection
     */
    private function buildWriter(?string &$messages): \Closure
    {
        return function (string $data) use (&$messages): void {
            str($data)->explode(PHP_EOL)->each(function (string $rowData) use (&$messages): void {
                // (正常|错误|流)响应
                $rowResponse = (array) json_decode(FoundationSDK::sanitizeData($rowData), true);
                $messages .= $text = $this->getCompletionMessages($rowResponse);
                $this->output->write($text);
            });
        };
    }
}
