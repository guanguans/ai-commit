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
use Illuminate\Support\Str;

final class OpenAIChatGenerator extends OpenAIGenerator
{
    /**
     * @psalm-suppress RedundantCast
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @noinspection MissingParentCallInspection
     * @noinspection PhpCastIsUnnecessaryInspection
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'user' => Str::uuid()->toString(),
        ] + Arr::get($this->config, 'parameters', []);

        $response = $this->openAI->chatCompletions($parameters, $this->buildWriter($messages));

        // fake 响应
        return (string) ($messages ?? $this->getCompletionMessages($response));
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompletionMessages($response): string
    {
        return Arr::get($response, 'choices.0.delta.content', '');
    }
}
