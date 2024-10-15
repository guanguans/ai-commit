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

use Symfony\Component\Process\Process;

final class GithubCopilotCliGenerator extends Generator
{
    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function generate(string $prompt): string
    {
        $output = resolve(
            Process::class,
            ['command' => [$this->config['binary'], 'copilot', 'explain', $prompt]] + $this->config['parameters']
        )->mustRun(function (string $type, string $data): void {
            Process::OUT === $type ? $this->output->write($data) : $this->output->write("<fg=red>$data</>");
        })->getOutput();

        return $this->sanitize($output);
    }
}
