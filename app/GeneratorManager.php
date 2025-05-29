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
use Illuminate\Config\Repository;
use Illuminate\Support\Manager;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin  \App\Contracts\GeneratorContract
 */
final class GeneratorManager extends Manager implements GeneratorContract
{
    use Conditionable;
    use Dumpable;
    use ForwardsCalls;
    use Localizable;
    use Macroable;
    use Tappable;

    public function getDefaultDriver(): string
    {
        return $this->config->get('ai-commit.generator');
    }

    public function generator(?string $generator = null): GeneratorContract
    {
        return $this->driver($generator);
    }

    public function generate(string $prompt): string
    {
        return $this->generator()->generate($prompt);
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection MissingParentCallInspection
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MethodVisibilityInspection
     */
    protected function createDriver(mixed $driver): GeneratorContract
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $config = new Repository($this->config->get("ai-commit.generators.$driver", []));
        $studlyName = str($config->get('driver', $driver))->replace('openai', 'OpenAI')->studly();

        if (class_exists($class = "App\\Generators\\{$studlyName}Generator")) {
            return new $class($config);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }
}
