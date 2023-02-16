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
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class OpenAIGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \App\Support\OpenAI
     */
    protected $openAI;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->openAI = new OpenAI(Arr::only($config, ['http_options', 'retry', 'base_url', 'api_key']));
        $this->output = $this->createOutput();
    }

    public function generate(string $prompt): string
    {
        $parameters = Arr::get($this->config, 'completion_parameters', []);
        $parameters['prompt'] = $prompt;

        $this->openAI->completions($parameters, function (string $data) use (&$messages): void {
            if (\str($data)->isJson()) {
                // 错误响应
                $response = json_decode($data, true);
                if (isset($response['error']['message'])) {
                    $this->output->writeln("<error>{$response['error']['message']}</error>");

                    return;
                }

                // 正常响应
                $text = Arr::get($response, 'choices.0.text', '');
                $messages .= $text;
                $this->output->write($text);

                return;
            }

            // 流响应
            $rowData = \str($data)->replaceFirst('data: ', '')->rtrim();
            if ($rowData->startsWith('[DONE]')) {
                return;
            }
            $text = Arr::get(json_decode((string) $rowData, true), 'choices.0.text', '');
            $messages .= $text;
            $this->output->write($text);
        });

        return (string) $messages;
    }

    protected function createOutput(): OutputInterface
    {
        try {
            $output = resolve(OutputInterface::class);
        } catch (\Throwable $e) {
            $output = resolve(ConsoleOutput::class);
        }

        return $output;
    }
}
