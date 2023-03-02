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

use App\Generators\OpenAIChatGenerator;
use App\Generators\OpenAIGenerator;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @method \App\Contracts\GeneratorContract driver(?string $driver = null)
 */
final class GeneratorManager extends Manager
{
    use Conditionable;
    use Tappable;

    public function getDefaultDriver()
    {
        return $this->config->get('ai-commit.generator');
    }

    /**
     * @noinspection MissingParentCallInspection
     */
    protected function createDriver($driver)
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $method = 'create'.Str::studly($driver).'Driver';
        if (method_exists($this, $method)) {
            return $this->$method($this->config->get("ai-commit.generators.$driver"));
        }

        throw new \InvalidArgumentException("Driver [$driver] not supported.");
    }

    protected function createOpenAIDriver(array $config): OpenAIGenerator
    {
        return new OpenAIGenerator($config);
    }

    protected function createOpenAIChatDriver(array $config): OpenAIChatGenerator
    {
        return new OpenAIChatGenerator($config);
    }
}
