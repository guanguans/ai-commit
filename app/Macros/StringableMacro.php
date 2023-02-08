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

use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Support\Stringable
 */
class StringableMacro
{
    public function isJson(): callable
    {
        return function () {
            return Str::isJson($this->value);
        };
    }
}
