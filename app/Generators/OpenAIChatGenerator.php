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

use Illuminate\Support\Arr;

final class OpenAIChatGenerator extends OpenAIGenerator
{
    /**
     * @psalm-suppress RedundantCast
     * @noinspection MissingParentCallInspection
     */
    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['messages'] = [
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->openAI->chatCompletions($parameters, $this->getWriter($messages));

        // fake 响应
        return (string) ($messages ?? self::extractCompletion($response));
    }

    /**
     * {@inheritdoc}
     */
    protected static function extractCompletion($response): string
    {
        return Arr::get($response, 'choices.0.delta.content', '');
    }
}
