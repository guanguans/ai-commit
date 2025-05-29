<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection JsonEncodingApiUsageInspection */
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

use App\ConfigManager;
use App\Exceptions\InvalidJsonFileException;
use App\Exceptions\UnsupportedConfigFileTypeException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

it('can create ConfigManager', function (): void {
    expect(ConfigManager::create())->toBeInstanceOf(ConfigManager::class)
        ->and(ConfigManager::create([]))->toBeInstanceOf(ConfigManager::class);
})->group(__DIR__, __FILE__);

it('can get local path', function (): void {
    $this->getFunctionMock(class_namespace(ConfigManager::class), 'getcwd')
        ->expects($this->once())
        ->willReturn(false);
    expect(ConfigManager::localPath())->toBeString();
})->group(__DIR__, __FILE__)->skip();

it('can put local config file', function (): void {
    expect(ConfigManager::create())->putLocal()->toBeInt();
})->group(__DIR__, __FILE__);

/**
 * @psalm-suppress UndefinedMagicMethod
 */
it('can to jsonSerialize', function (): void {
    /** @noinspection ReplaceLegacyMockeryInspection */
    $configManager = ConfigManager::create([
        'JsonSerializable' => Mockery::spy(JsonSerializable::class),
        'Jsonable' => Mockery::spy(Jsonable::class)->shouldReceive('toJson')->andReturn(json_encode([1, 2, 3]))->getMock(),
        'Arrayable' => Mockery::spy(Arrayable::class),
    ]);
    expect($configManager)->jsonSerialize()->toBeArray();
})->group(__DIR__, __FILE__);

it('can to array', function (): void {
    expect(ConfigManager::create())->toArray()->toBeArray();
})->group(__DIR__, __FILE__);

it('can to string', function (): void {
    expect((string) ConfigManager::create())->toBeString();
})->group(__DIR__, __FILE__);

it('will throw InvalidJsonFileException when read from config file', function (): void {
    ConfigManager::readFrom(fixtures_path('ai-commit.json'));
})->group(__DIR__, __FILE__)->throws(InvalidJsonFileException::class);

it('will throw UnsupportedConfigFileTypeException when read from config file', function (): void {
    ConfigManager::readFrom(fixtures_path('ai-commit.yml'));
})->group(__DIR__, __FILE__)->throws(UnsupportedConfigFileTypeException::class);
