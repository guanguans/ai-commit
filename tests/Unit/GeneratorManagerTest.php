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
use App\Generators\OpenAIGenerator;

it('get default driver', function () {
    $defaultDriver = $this->app->get(GeneratorManager::class)->getDefaultDriver();
    expect($defaultDriver)->toBeString();
});

it('create open a i driver', function () {
    $driver = $this->app->get(GeneratorManager::class)->driver('openai');
    expect($driver)->toBeInstanceOf(OpenAIGenerator::class);
});
