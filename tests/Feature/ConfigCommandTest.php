<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Feature;

use Tests\TestCase;

class ConfigCommandTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testConfigCommand(): void
    {
        $this->artisan('config get')
            // ->expectsOutput('Simplicity is the ultimate sophistication.')
            ->assertExitCode(0);
    }
}
