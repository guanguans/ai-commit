<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\GeneratorManager;
use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function (): void {
});

it('throws `ProcessFailedException`', function (): void {
    expect(app(GeneratorManager::class)->driver('bito'))->generate('error');
})->group(__DIR__, __FILE__)->throws(ProcessFailedException::class);
