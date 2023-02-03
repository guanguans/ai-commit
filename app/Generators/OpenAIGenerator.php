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
use App\Contracts\OutputAwareContract;
use App\Generators\Concerns\OutputAware;
use App\Support\OpenAI;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Output\OutputInterface;

class OpenAIGenerator implements GeneratorContract, OutputAwareContract
{
    use OutputAware;

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

        $this->openAI->completions($parameters, function (string $data) use (&$commitMessages) {
            $stringable = \str($data)->replaceFirst('data: ', '')->rtrim();
            if ($stringable->startsWith('[DONE]')) {
                return;
            }

            $text = Arr::get(json_decode((string) $stringable, true), 'choices.0.text', '');
            $commitMessages .= $text;
            app(OutputInterface::class)->write($text);
        });

        return (string) $commitMessages;
    }
}
