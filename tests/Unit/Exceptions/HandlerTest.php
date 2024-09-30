<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

it('can map ValidationException', function (): void {
    expect(app(ExceptionHandler::class))
        ->report(
            new ValidationException($this->app->get(Factory::class)->make(['foo' => 'bar'], ['foo' => 'int']))
        )->toBeNull();
})->group(__DIR__, __FILE__);
