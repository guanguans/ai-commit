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

use App\ConfigManager;
use App\Contracts\GeneratorContract;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Process\Process;

class BitoGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->output = resolve(OutputStyle::class);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress UnusedClosureParam
     */
    public function generate(string $prompt): string
    {
        file_put_contents($promptFile = ConfigManager::globalPath($this->config['prompt_filename']), $prompt);

        return resolve(Process::class, ['command' => ['bito', '-p', $promptFile]] + $this->config['parameters'])
            ->mustRun(function (string $type, string $data): void {
                Process::OUT === $type ? $this->output->write($data) : $this->output->write("<fg=red>$data</>");
            })
            ->getOutput();
    }
}
