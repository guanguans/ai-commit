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

namespace App\Generators;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

final class ErnieBotTurboGenerator extends ErnieBotGenerator
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @throws BindingResolutionException
     * @throws RequestException
     */
    protected function completion(array $parameters, ?callable $writer = null): Response
    {
        return $this->ernie->ernieBotTurbo($parameters, $writer);
    }
}
