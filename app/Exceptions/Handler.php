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

use Illuminate\Validation\ValidationException;

final class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * {@inheritdoc}
     *
     * @psalm-suppress UndefinedThisPropertyAssignment
     */
    public function renderForConsole($output, \Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            (function (ValidationException $e): void {
                $this->message = ($prefix = '- ').implode(PHP_EOL.$prefix, $e->validator->errors()->all());
            })->call($e, $e);
        }

        parent::renderForConsole($output, $e);
    }

    /**
     * {@inheritdoc}
     */
    protected function shouldntReport(\Throwable $e)
    {
        if ($this->container->isProduction()) {
            return true;
        }

        return parent::shouldntReport($e);
    }
}
