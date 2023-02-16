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

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * {@inheritdoc}
     */
    protected $dontReport = [];

    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, \Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            $e = new InvalidConfigException(
                $e->validator->errors()->first(),
                $e->status,
                $e->getPrevious()
            );
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
