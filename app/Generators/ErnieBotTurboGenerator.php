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

class ErnieBotTurboGenerator extends ErnieBotGenerator
{
    protected function getResponse(array $parameters): array
    {
        $response = $this->ernie->ernieBotTurbo($parameters, $this->buildWriter($messages));

        return [$messages, $response];
    }
}
