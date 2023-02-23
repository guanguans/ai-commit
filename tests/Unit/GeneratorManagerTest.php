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

it('can get default driver name', function () {
    expect($this->app->get(GeneratorManager::class))
        ->getDefaultDriver()->toBeString();
})->group(__DIR__, __FILE__);

it('can create OpenAI driver', function () {
    expect($generatorManager = $this->app->get(GeneratorManager::class))
        ->driver('openai')->toBeInstanceOf(OpenAIGenerator::class);

    $generatorManager->extend('foo', function () {
        return new OpenAIGenerator(config('ai-commit.generators.openai'));
    });
    expect($generatorManager)
        ->driver('foo')->toBeInstanceOf(OpenAIGenerator::class);
})->group(__DIR__, __FILE__);

it('will throw `InvalidArgumentException`', function () {
    $this->app->get(GeneratorManager::class)->driver('foo');
})->group(__DIR__, __FILE__)->throws(InvalidArgumentException::class, 'Driver [foo] not supported.');
