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

namespace App\Macros;

/**
 * @mixin \Illuminate\Support\Collection
 */
class CollectionMacro
{
    /**
     * @noinspection JsonEncodingApiUsageInspection
     * @noinspection PhpMethodParametersCountMismatchInspection
     *
     * @psalm-suppress UnusedClosureParam
     * @psalm-suppress TooManyArguments
     */
    public static function json(): callable
    {
        return static function (string $json, int $depth = 512, int $options = 0): self {
            return new static(json_decode($json, true, $depth, $options));
        };
    }
}
