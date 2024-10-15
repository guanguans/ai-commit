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

use App\Contracts\GeneratorContract;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Generator implements GeneratorContract
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $outputStyle;

    /**
     * @var \Symfony\Component\Console\Helper\ProcessHelper
     */
    protected $processHelper;

    /**
     * @psalm-suppress UndefinedMethod
     * @noinspection PhpUndefinedMethodInspection
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->outputStyle = tap(clone resolve(OutputStyle::class))->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->processHelper = (function () {
            return $this->getArtisan()->getHelperSet()->get('process');
        })->call(Artisan::getFacadeRoot());
    }
}
