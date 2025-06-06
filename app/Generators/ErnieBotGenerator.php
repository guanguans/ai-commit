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

use App\Clients\Ernie;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ErnieBotGenerator extends AbstractGenerator
{
    protected Ernie $ernie;

    public function __construct(Repository $config)
    {
        parent::__construct($config);
        $this->ernie = new Ernie($config->all());
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'user_id' => Str::uuid()->toString(),
        ] + $this->config->get('parameters', []);

        $response = $this->completion($parameters, $this->buildWriter($messages));

        // fake 响应
        return $messages ?? $this->getCompletionMessages($response);
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws RequestException
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

    private function getCompletionMessages(array|\ArrayAccess $response): string
    {
        return Arr::get($response, 'result', '');
    }
}
