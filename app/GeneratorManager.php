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

class GeneratorManager extends Manager
{
    /**
     * @param string|null $driver
     *
     * @return \App\Contracts\GeneratorContract
     */
    public function driver($driver = null)
    {
        return parent::driver($driver);
    }

    public function getDefaultDriver()
    {
        return $this->config->get('ai-commit.generator');
    }

    protected function createOpenAIDriver(): OpenAIGenerator
    {
        return new OpenAIGenerator($this->config->get('ai-commit.generators.openai'));
    }
}
