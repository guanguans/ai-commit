<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Generators\Concerns;

use Symfony\Component\Console\Style\SymfonyStyle;

trait OutputAware
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $output;

    public function setOutput(SymfonyStyle $output): void
    {
        $this->output = $output;
    }
}
