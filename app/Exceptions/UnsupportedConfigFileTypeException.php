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

class UnsupportedConfigFileTypeException extends InvalidArgumentException
{
    public static function make(string $file, int $code = 0, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('The config file type(%s) is not supported', pathinfo($file, PATHINFO_EXTENSION)),
            $code,
            $previous
        );
    }
}
