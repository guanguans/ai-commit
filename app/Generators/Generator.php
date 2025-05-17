<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Generators;

use App\Contracts\GeneratorContract;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class Generator implements GeneratorContract
{
    protected OutputStyle $output;
    protected HelperSet $helperSet;

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function __construct(protected array $config)
    {
        $this->output = tap(clone resolve(OutputStyle::class))->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->helperSet = (fn () => $this->getArtisan()->getHelperSet())->call(Artisan::getFacadeRoot());
    }

    /**
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    protected function mustRunProcess(
        array|Process|string $cmd,
        ?string $error = null,
        ?callable $callback = null,
        int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
        ?OutputInterface $output = null
    ): Process {
        $process = $this->runProcess($cmd, $error, $callback, $verbosity, $output);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }

    /**
     * @noinspection MissingParameterTypeDeclarationInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function runProcess(
        array|Process|string $cmd,
        ?string $error = null,
        ?callable $callback = null,
        int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
        ?OutputInterface $output = null
    ): Process {
        if (\is_string($cmd)) {
            $cmd = Process::fromShellCommandline($cmd);
        }

        return $this->getHelper('process')->run($output ?? $this->output, $cmd, $error, $callback, $verbosity);
    }

    /**
     * @throws InvalidArgumentException if the helper is not defined
     */
    protected function getHelper(string $name): HelperInterface
    {
        return $this->helperSet->get($name);
    }

    protected function runningCallback(): callable
    {
        return function (string $type, string $data): void {
            Process::OUT === $type ? $this->output->write($data) : $this->output->write("<fg=red>$data</>");
        };
    }

    protected function ensureWithOptions(array $command): array
    {
        return array_merge($command, $this->hydratedOptions());
    }

    /**
     * @return list<string>
     */
    protected function hydratedOptions(): array
    {
        return collect($this->config['options'] ?? [])
            ->map(static fn ($value): string => (string) str(urldecode(http_build_query([$option = 'option' => $value])))->after("$option="))
            ->filter()
            ->map(static fn ($value, string $option): array => [$option, $value])
            ->flatten()
            ->all();
    }
}
