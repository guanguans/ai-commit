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
use App\Clients\OpenAI;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class OpenAIGenerator extends AbstractGenerator
{
    protected OpenAI $openAI;

    public function __construct(Repository $config)
    {
        parent::__construct($config);
        $this->openAI = new OpenAI($config->all());
    }

    /**
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
     * ```.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function generate(string $prompt): string
    {
        $parameters = [
            'prompt' => $prompt,
            'user' => Str::uuid()->toString(),
        ] + $this->config->get('parameters', []);

        $response = $this->openAI->completions($parameters, $this->buildWriter($messages));

        // fake 响应
        return $messages ?? $this->getCompletionMessages($response);
    }

    protected function getCompletionMessages(array|\ArrayAccess $response): string
    {
        return Arr::get($response, 'choices.0.text', '');
    }

    /**
     * @noinspection JsonEncodingApiUsageInspection
     */
    protected function buildWriter(?string &$messages): \Closure
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
