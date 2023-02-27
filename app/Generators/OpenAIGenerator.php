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
use App\Support\OpenAI;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;

final class OpenAIGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \App\Support\OpenAI
     */
    private $openAI;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->openAI = new OpenAI(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
    }

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
     */
    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['prompt'] = $prompt;
        $output = resolve(OutputStyle::class);

        $response = $this->openAI
            ->completions($parameters, function (string $data) use ($output, &$messages): void {
                // 流响应完成
                if (\str($data)->startsWith('data: [DONE]')) {
                    return;
                }

                // (正常|错误|流)响应
                $rowResponse = (array) json_decode($this->openAI::clearBody($data), true);
                $messages .= $text = Arr::get($rowResponse, 'choices.0.text', '');
                $output->write($text);
            });

        // fake 响应
        return (string) ($messages ?: $response->json('choices.0.text'));
    }
}
