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
    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Throwable::class,
    ];

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress UndefinedThisPropertyAssignment
     * @psalm-suppress UndefinedThisPropertyFetch
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress UnusedClosureParam
     *
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

        $this->reportable(static function (\Throwable $throwable): bool {return false; })->stop();
    }
}
