<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Contracts;

use Symfony\Component\Console\Style\SymfonyStyle;

interface OutputAwareContract
{
    public function setOutput(SymfonyStyle $output): void;
}
