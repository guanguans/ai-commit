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

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Style\SymfonyStyle;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, \Throwable $e): void
    {
        $outputStyle = $this->container->make(SymfonyStyle::class);
        $note = sprintf("In %s line {$e->getLine()}:", pathinfo($e->getFile(), PATHINFO_FILENAME));

        if ($e instanceof ValidationException) {
            $outputStyle->note($note);
            $outputStyle->block($e->validator->errors()->all(), 'ERROR(Config)', 'fg=white;bg=red', ' ', true);

            return;
        }

        if ($e instanceof HttpClientException) {
            $status = trans($key = "http-statuses.{$e->getCode()}");
            if ($key !== $status) {
                $outputStyle->note($note);
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
        if (\Phar::running()) {
            return true;
        }

        return parent::shouldntReport($e);
    }
}
