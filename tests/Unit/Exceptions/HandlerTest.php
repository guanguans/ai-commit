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

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

it('can map ValidationException', function (): void {
    expect(app(ExceptionHandler::class))
        ->report(
            new ValidationException($this->app->get(Factory::class)->make(['foo' => 'bar'], ['foo' => 'int']))
        )->toBeNull();
})->group(__DIR__, __FILE__);
