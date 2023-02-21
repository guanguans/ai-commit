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

use Illuminate\Console\OutputStyle;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Validation\ValidationException;

final class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, \Throwable $e): void
    {
        $outputStyle = $this->container->make(OutputStyle::class);
        $position = sprintf("In %s line {$e->getLine()}:", pathinfo($e->getFile(), PATHINFO_FILENAME));

        if ($e instanceof ValidationException) {
            $outputStyle->section($position);
            $outputStyle->block($e->validator->errors()->all(), 'ERROR(Config)', 'fg=white;bg=red', ' ', true);

            return;
        }

        if ($e instanceof HttpClientException) {
            $status = trans($key = "http-statuses.{$e->getCode()}");
            if ($key !== $status) {
                $outputStyle->section($position);
                $outputStyle->block($e->getMessage(), "ERROR($status)", 'fg=white;bg=red', ' ', true);

                return;
            }
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
