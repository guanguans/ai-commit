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
use App\Support\FoundationSDK;
use App\Support\OpenAI;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class OpenAIGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \App\Support\OpenAI
     */
    protected $openAI;

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $outputStyle;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->openAI = new OpenAI(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
        $this->outputStyle = resolve(OutputStyle::class);
    }

    /**
     * @psalm-suppress RedundantCast
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
        $parameters = [
            'prompt' => $prompt,
            'user' => Str::uuid()->toString(),
        ] + Arr::get($this->config, 'parameters', []);

        $response = $this->openAI->completions($parameters, $this->buildWriter($messages));

        // fake 响应
        return (string) ($messages ?? $this->getCompletionMessages($response));
    }

    /**
     * @param array|\ArrayAccess $response
     */
    protected function getCompletionMessages($response): string
    {
        return Arr::get($response, 'choices.0.text', '');
    }

    /**
     * @noinspection JsonEncodingApiUsageInspection
     */
    protected function buildWriter(?string &$messages): \Closure
    {
        return function (string $data) use (&$messages): void {
            str($data)->explode(PHP_EOL)->each(function (string $rowData) use (&$messages): void {
                // (正常|错误|流)响应
                $rowResponse = (array) json_decode(FoundationSDK::sanitizeData($rowData), true);
                $messages .= $text = $this->getCompletionMessages($rowResponse);
                $this->outputStyle->write($text);
            });
        };
    }
}
