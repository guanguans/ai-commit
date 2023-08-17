<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Generators;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class ErnieBotTurboGenerator extends ErnieBotGenerator
{
    /**
     * @throws RequestException
     * @throws BindingResolutionException
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function completion(array $parameters, ?callable $writer = null): Response
    {
        return $this->ernie->ernieBotTurbo($parameters, $writer);
    }
}
