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
use Symfony\Component\Process\Process;

final class BitoCliGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    private $outputStyle;

    /**
     * @var \Symfony\Component\Console\Helper\ProcessHelper
     */
    private $processHelper;

    /**
     * @psalm-suppress UndefinedMethod
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->outputStyle = tap(clone resolve(OutputStyle::class))->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->processHelper = (function () {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->getArtisan()->getHelperSet()->get('process');
        })->call(Artisan::getFacadeRoot());
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function generate(string $prompt): string
    {
        return $this
            ->processHelper
            ->mustRun(
                $this->outputStyle,
                resolve(
                    Process::class,
                    ['command' => [$this->config['binary']]] + $this->config['parameters']
                )->setInput($prompt)
            )
            ->getOutput();
    }
}
