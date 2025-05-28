<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App;

use App\Contracts\GeneratorContract;
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

    public function getDefaultDriver(): string
    {
        return $this->config->get('ai-commit.generator');
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection MissingParentCallInspection
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function createDriver(mixed $driver): GeneratorContract
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        /** @var array $config */
        $config = $this->config->get("ai-commit.generators.$driver");

        $studlyName = Str::studly($config['driver'] ?? $driver);

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
