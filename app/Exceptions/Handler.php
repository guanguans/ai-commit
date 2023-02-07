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

use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\ValidationException;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    ];

    public function renderForConsole($request, \Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $e = new InvalidConfigException($e->validator->errors()->first());
        }

        if ($e instanceof RequestException) {
        }

        parent::renderForConsole($request, $e);
    }

    public function shouldntReport(\Throwable $e)
    {
        if ($this->container->isProduction()) {
            return true;
        }

        return parent::shouldntReport($e);
    }
}
