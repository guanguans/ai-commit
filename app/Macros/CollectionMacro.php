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
