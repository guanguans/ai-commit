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

use App\Commands\ConfigCommand;

class UnsupportedActionOfConfigException extends InvalidArgumentException
{
    public static function make(string $action, int $code = 0, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf("The action($action) is not supported, that must be one of [%s].", implode(', ', ConfigCommand::ACTIONS)),
            $code,
            $previous
        );
    }
}
