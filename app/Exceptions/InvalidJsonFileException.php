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

final class InvalidJsonFileException extends InvalidArgumentException
{
    public static function make(string $file, int $code = 0, ?\Throwable $previous = null): self
    {
        return new self("The file($file) is an invalid json file.", $code, $previous);
    }
}
