<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App;

use App\Generators\OpenAIGenerator;
use Illuminate\Support\Manager;

/**
 * @method \App\Contracts\GeneratorContract driver(?string $driver = null)
 */
class GeneratorManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('ai-commit.generator');
    }

    protected function createOpenAIDriver(): OpenAIGenerator
    {
        return new OpenAIGenerator($this->config->get('ai-commit.generators.openai'));
    }
}
