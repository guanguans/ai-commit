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

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class OpenAIChatGenerator extends OpenAIGenerator
{
    /**
     * @noinspection MissingParentCallInspection
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'user' => Str::uuid()->toString(),
        ] + $this->config->get('parameters', []);

        $response = $this->openAI->chatCompletions($parameters, $this->buildWriter($messages));

        // fake 响应
        return $messages ?? $this->getCompletionMessages($response);
    }

    /**
     * {}.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getCompletionMessages(array|\ArrayAccess $response): string
    {
        return Arr::get($response, 'choices.0.delta.content', '');
    }
}
