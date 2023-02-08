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
use Illuminate\Support\Arr;
use Symfony\Component\Console\Output\OutputInterface;

class OpenAIGenerator implements GeneratorContract
{
    /**
     * @var \App\Support\OpenAI
     */
    protected $openAI;

    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->openAI = new OpenAI(Arr::only($config, ['http_options', 'api_key']));
    }

    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['prompt'] = $prompt;

        $this->openAI->completions($parameters, function (string $data) use (&$messages) {
            $output = resolve(OutputInterface::class);
            if (is_json($data)) {
                // 错误响应
                $response = json_decode($data, true);
                if (isset($response['error']['message'])) {
                    $output->write(PHP_EOL);
                    $output->write("<error>{$response['error']['message']}</error>");
                    $output->write(PHP_EOL);

                    return;
                }

                // 正常响应
                $text = Arr::get($response, 'choices.0.text', '');
                $messages .= $text;
                $output->write($text);

                return;
            }

            // 流响应
            $stringable = \str($data)->replaceFirst('data: ', '')->rtrim();
            if ($stringable->startsWith('[DONE]')) {
                return;
            }

            $text = Arr::get(json_decode((string) $stringable, true), 'choices.0.text', '');
            $messages .= $text;
            $output->write($text);
        });

        return (string) $messages;
    }
}
