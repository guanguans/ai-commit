<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnused */
/** @noinspection SqlResolve */
declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\GeneratorManager;
use App\Generators\BitoCliGenerator;
use App\Generators\OpenAIChatGenerator;
use App\Generators\OpenAIGenerator;

it('can get default driver name', function (): void {
    expect($this->app->get(GeneratorManager::class))
        ->getDefaultDriver()->toBeString();
})->group(__DIR__, __FILE__);

it('can create OpenAI driver', function (): void {
    expect($generatorManager = $this->app->get(GeneratorManager::class))
        ->driver('openai')->toBeInstanceOf(OpenAIGenerator::class);

    $generatorManager->extend('foo', fn (): OpenAIGenerator => new OpenAIGenerator(config('ai-commit.generators.openai')));
    expect($generatorManager)
        ->driver('foo')->toBeInstanceOf(OpenAIGenerator::class);
})->group(__DIR__, __FILE__);

it('can create OpenAI chat driver', function (): void {
    expect($this->app->get(GeneratorManager::class))
        ->driver('openai_chat')->toBeInstanceOf(OpenAIChatGenerator::class);
})->group(__DIR__, __FILE__);

it('can create Bito Cli driver', function (): void {
    expect($this->app->get(GeneratorManager::class))
        ->driver('bito_cli')->toBeInstanceOf(BitoCliGenerator::class);
})->group(__DIR__, __FILE__);

it('will throw InvalidArgumentException when run driver', function (): void {
    $this->app->get(GeneratorManager::class)->driver('foo');
})->group(__DIR__, __FILE__)->throws(InvalidArgumentException::class, 'Driver [foo] not supported.');
