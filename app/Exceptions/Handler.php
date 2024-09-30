<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

/**
 * @property string $message
 * @property \Illuminate\Validation\Validator $validator
 */
final class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress UndefinedThisPropertyAssignment
     * @psalm-suppress UndefinedThisPropertyFetch
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress UnusedClosureParam
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection PhpUnusedParameterInspection
     */
    public function register(): void
    {
        $this->map(ValidationException::class, function (ValidationException $validationException) {
            return (function (): ValidationException {
                $this->message = PHP_EOL.($prefix = '- ').implode(PHP_EOL.$prefix, $this->validator->errors()->all());

                /** @noinspection PhpIncompatibleReturnTypeInspection */
                return $this;
            })->call($validationException);
        });

        $this->reportable(function (\Throwable $throwable): bool {
            return ! $this->container->isProduction();
        });
    }
}
