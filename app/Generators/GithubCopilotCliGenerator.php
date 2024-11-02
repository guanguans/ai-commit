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
    public function generate(string $prompt): string
    {
        return resolve(
            Process::class,
            [
                'command' => array_merge(
                    [$this->config['binary'], 'copilot', 'explain', $prompt],
                    $this->defaultHydratedOptions()
                ),
            ] + $this->config['parameters']
        )->mustRun($this->defaultRunningCallback())->getOutput();
    }
}
