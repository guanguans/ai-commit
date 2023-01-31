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
use Throwable;

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

    public function report(Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $e = new InvalidConfigException($e->validator->errors()->first());
        }

        if ($e instanceof RequestException) {
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $e = new InvalidConfigException($e->validator->errors()->first());
        }

        if ($e instanceof RequestException) {
        }

        parent::render($request, $e);
    }

    public function renderForConsole($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $e = new InvalidConfigException($e->validator->errors()->first());
        }

        if ($e instanceof RequestException) {
        }

        parent::renderForConsole($request, $e);
    }
}
