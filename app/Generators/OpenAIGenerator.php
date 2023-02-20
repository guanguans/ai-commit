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

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    private $output;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->openAI = new OpenAI(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
        $this->output = resolve(OutputStyle::class);
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['prompt'] = $prompt;

        $this->openAI->completions($parameters, function (string $data) use (&$messages): void {
            if (\str($data)->isJson()) {
                // 错误响应
                $response = json_decode($data, true);
                if (isset($response['error']['message'])) {
                    $this->output->section(sprintf('In %s line %s:', pathinfo(__FILE__, PATHINFO_FILENAME), __LINE__));
                    $this->output->error($response['error']['message']);

                    return;
                }

                // 正常响应
                $text = Arr::get($response, 'choices.0.text', '');
                $messages .= $text;
                $this->output->write($text);

                return;
            }

            // 流响应
            $data = \str($data)->replaceFirst('data: ', '')->rtrim();
            if ($data->startsWith('[DONE]')) {
                return;
            }
            $text = Arr::get(json_decode((string) $data, true), 'choices.0.text', '');
            $messages .= $text;
            $this->output->write($text);
        });

        return (string) $messages;
    }
}
