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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

    /**
     * @param array|string|\Symfony\Component\Process\Process $cmd
     */
    public function processHelperMustRun(
        $cmd,
        ?string $error = null,
        ?callable $callback = null,
        int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
        ?OutputInterface $output = null
    ): Process {
        /** @var Process $process */
        $process = $this->processHelperRun($cmd, $error, $callback, $verbosity, $output);
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }

    /**
     * @param array|string|\Symfony\Component\Process\Process $cmd
     */
    public function processHelperRun(
        $cmd,
        ?string $error = null,
        ?callable $callback = null,
        int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
        ?OutputInterface $output = null
    ): Process {
        if (\is_string($cmd)) {
            $cmd = Process::fromShellCommandline($cmd);
        }

        /** @var \Symfony\Component\Console\Helper\ProcessHelper $helper */
        $helper = $this->getHelper('process');

        return $helper->run($output ?? $this->output, $cmd, $error, $callback, $verbosity);
    }

    protected function sanitize(string $output): string
    {
        return (string) str($output)
            ->match('/\{.*\}/s')
            // ->replaceMatches('/[[:cntrl:]]/mu', '')
            ->replace(
                ["\\'", PHP_EOL],
                ["'", '']
            );
    }
}
