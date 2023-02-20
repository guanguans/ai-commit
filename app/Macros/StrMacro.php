<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Macros;

/**
 * @mixin \Illuminate\Support\Str
 */
final class StrMacro
{
    /**
     * @psalm-suppress UnusedFunctionCall
     */
    public static function isJson(): \Closure
    {
        return static function ($value): bool {
            if (! is_string($value)) {
                return false;
            }

            json_decode($value);

            return JSON_ERROR_NONE === json_last_error();
        };
    }
}
