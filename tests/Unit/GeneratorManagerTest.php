<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Unit;

use App\GeneratorManager;
use App\Generators\OpenAIGenerator;
use Tests\TestCase;

class GeneratorManagerTest extends TestCase
{
    public function testGetDefaultDriver(): void
    {
        $defaultDriver = $this->app->get(GeneratorManager::class)->getDefaultDriver();
        $this->assertIsString($defaultDriver);
    }

    public function testCreateOpenAIDriver(): void
    {
        $driver = $this->app->get(GeneratorManager::class)->driver('openai');
        $this->assertInstanceOf(OpenAIGenerator::class, $driver);
    }
}
