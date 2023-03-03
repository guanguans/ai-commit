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

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;

final class OpenAIChatGenerator extends OpenAIGenerator
{
    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     *
     * ```return
     * [
     *     {
     *         "id": 1,
     *         "subject": "patch(Models/Example.php): Update variable value",
     *         "body": "- Update value of `$var1` from `123` to `456`\n- Patched by `composer-patches`"
     *     },
     *     {
     *         "id": 2,
     *         "subject": "chore(Models/Example.php): Refactor someFunction",
     *         "body": "- Refactor `someFunction`\n- Replace value of `$var1` from `123` to `456`"
     *     },
     *     {
     *         "id": 3,
     *         "subject": "Patch(Models/Example.php): Modify var1",
     *         "body": "- Patched by composer-patches\n- Changed value of var1 from 123 to 456"
     *     }
     * ]
     * ```
     * @noinspection MissingParentCallInspection
     *
     * @psalm-suppress UnusedVariable
     */
    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['messages'] = [
            ['role' => 'assistant', 'content' => $prompt],
        ];
        $output = resolve(OutputStyle::class);

        $response = $this->openAI
            ->chatCompletions($parameters, function (string $data) use ($output, &$messages): void {
                // 流响应完成
                if (\str($data)->startsWith('data: [DONE]')) {
                    return;
                }

                // (正常|错误|流)响应
                $rowResponse = (array) json_decode($this->openAI::hydrateData($data), true);
                $messages .= $text = Arr::get($rowResponse, 'choices.0.delta.content', '');
                $output->write($text);
            });

        // fake 响应
        return (string) ($messages ?? $response->json('choices.0.delta.content'));
    }
}
