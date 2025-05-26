<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Mixins;

/**
 * @mixin \Illuminate\Support\Str
 */
final class StrMixin
{
    /**
     * @see https://github.com/symfony/polyfill-php83
     *
     * @noinspection JsonEncodingApiUsageInspection
     * @noinspection BadExceptionsProcessingInspection
     */
    public static function jsonValidate(): \Closure
    {
        return static function (string $json, int $depth = 512, int $flags = 0): bool {
            if (0 !== $flags && \defined('JSON_INVALID_UTF8_IGNORE') && \JSON_INVALID_UTF8_IGNORE !== $flags) {
                throw new \ValueError('json_validate(): Argument #3 ($flags) must be a valid flag (allowed flags: JSON_INVALID_UTF8_IGNORE)');
            }

            if (0 >= $depth) {
                throw new \ValueError('json_validate(): Argument #2 ($depth) must be greater than 0');
            }

            // see https://www.php.net/manual/en/function.json-decode.php
            if ($depth > ($jsonMaxDepth = 0x7FFFFFFF)) {
                throw new \ValueError(\sprintf('json_validate(): Argument #2 ($depth) must be less than %d', $jsonMaxDepth));
            }

            json_decode($json, null, $depth, $flags);

            return \JSON_ERROR_NONE === json_last_error();
        };
    }
}
