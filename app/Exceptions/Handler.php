<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

/**
 * @property string $message
 * @property \Illuminate\Validation\Validator $validator
 */
final class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    protected $dontReport = [
        \Throwable::class,
    ];

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function register(): void
    {
        $this->map(ValidationException::class, fn (ValidationException $validationException) => (function (): ValidationException {
            $this->message = \PHP_EOL.($prefix = '- ').implode(\PHP_EOL.$prefix, $this->validator->errors()->all());

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this;
        })->call($validationException));

        $this->reportable(static fn (\Throwable $throwable): bool => false)->stop();
    }
}
