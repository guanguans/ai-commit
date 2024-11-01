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

final class GithubModelsCliGenerator extends Generator
{
    public function generate(string $prompt): string
    {
        return resolve(
            Process::class,
            [
                'command' => collect([
                    $this->config['binary'],
                    'models',
                    'run',
                    $this->config['model'],
                    $prompt,
                ])->merge(
                    collect($this->config['options'])
                        ->filter(static function ($value): bool {
                            return null !== $value && '' !== $value;
                        })
                        ->map(static function ($value, string $key): array {
                            return [$key, $value];
                        })
                        ->flatten()
                )->all(),
            ] + $this->config['parameters']
        )->mustRun($this->defaultRunningCallback())->getOutput();
    }
}
