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
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class GithubCopilotCliGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    private $outputStyle;

    /**
     * @psalm-suppress UndefinedMethod
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->outputStyle = tap(clone resolve(OutputStyle::class))->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function generate(string $prompt): string
    {
        $output = resolve(
            Process::class,
            ['command' => [$this->config['binary'], 'copilot', 'explain', $prompt]] + $this->config['parameters']
        )->mustRun(function (string $type, string $data): void {
            Process::OUT === $type ? $this->outputStyle->write($data) : $this->outputStyle->write("<fg=red>$data</>");
        })->getOutput();

        return (string) str($output)
            ->dump()
            ->match('/\{.*\}/s')
            ->dump()
            ->replace(
                ["\\'", PHP_EOL],
                ["'", '']
            )
            ->dump()
            // ->replaceMatches('/[[:cntrl:]]/mu', '')
            ->dump();
    }
}
