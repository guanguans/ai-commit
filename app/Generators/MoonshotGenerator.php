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

namespace App\Generators;

use App\Clients\AbstractClient;
use App\Clients\Moonshot;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

final class MoonshotGenerator extends AbstractGenerator
{
    private readonly Moonshot $moonshot;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->moonshot = new Moonshot(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
    }

    /**
     * @noinspection PhpCastIsUnnecessaryInspection
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\RequestException
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
        return $messages ?? $this->getCompletionMessages($response);
    }

    private function getCompletionMessages(array|Response $response): string
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
            str($data)->explode(\PHP_EOL)->each(function (string $rowData) use (&$messages): void {
                // (正常|错误|流)响应
                $rowResponse = (array) json_decode(AbstractClient::sanitizeData($rowData), true);
                $messages .= $text = $this->getCompletionMessages($rowResponse);
                $this->output->write($text);
            });
        };
    }
}
