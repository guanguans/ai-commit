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
        $globalPath = ConfigManager::globalPath('bito.prompt');
        file_put_contents($globalPath, $prompt);

        return Process::fromShellCommandline("bito -p $globalPath")
            ->mustRun(function ($type, $data): void {
                $this->output->write($data);
            })
            ->getOutput();
    }
}
