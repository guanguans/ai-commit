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

use App\Exceptions\InvalidArgumentException;
use App\Generators\OpenAIChatGenerator;
use App\Generators\OpenAIGenerator;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin  \App\Contracts\GeneratorContract
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
     * {@inheritDoc}
     *
     * @noinspection MissingParentCallInspection
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function createDriver($driver)
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $config = $this->config->get("ai-commit.generators.$driver");
        $studlyName = Str::studly($driver);

        if (method_exists($this, $method = "create{$studlyName}Driver")) {
            return $this->{$method}($config);
        }

        if (class_exists($class = "App\\Generators\\{$studlyName}Generator")) {
            return new $class($config);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    private function createOpenAIDriver(array $config): OpenAIGenerator
    {
        return new OpenAIGenerator($config);
    }

    private function createOpenAIChatDriver(array $config): OpenAIChatGenerator
    {
        return new OpenAIChatGenerator($config);
    }
}
