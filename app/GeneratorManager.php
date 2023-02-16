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

use App\Contracts\OutputAwareContract;
use App\Generators\OpenAIGenerator;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @method \App\Contracts\GeneratorContract driver(?string $driver = null)
 */
class GeneratorManager extends Manager
{
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
            $generator = $this->$method($this->config->get("ai-commit.generators.$driver"));
            if ($generator instanceof OutputAwareContract) {
                $generator->setOutput($this->container->make(SymfonyStyle::class));
            }

            return $generator;
        }

        throw new \InvalidArgumentException("Driver [$driver] not supported.");
    }

    protected function createOpenAIDriver(array $config): OpenAIGenerator
    {
        return new OpenAIGenerator($config);
    }
}
