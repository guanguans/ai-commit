<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Generators;

use Symfony\Component\Process\Process;

final class BitoCliGenerator extends AbstractGenerator
{
    public function generate(string $prompt): string
    {
        return $this
            ->mustRunProcess(
                resolve(
                    Process::class,
                    [
                        'command' => $this->ensureWithOptions([$this->config->get('binary')]),
                    ] + $this->config->get('parameters', [])
                )->setInput($prompt)
            )
            ->getOutput();
    }
}
