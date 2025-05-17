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

namespace App\Commands;

use App\Exceptions\InvalidArgumentException;
use Illuminate\Console\Application as Artisan;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @see \LaravelZero\Framework\Commands\BuildCommand
 * @see https://github.com/configuredco/haulable
 */
final class BuildCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected $signature = 'app:build
        {name? : The build name}
        {--build-version= : The build version, if not provided it will be asked}
        {--timeout=300 : The timeout in seconds or 0 to disable}
    ';

    /**
     * {@inheritDoc}
     */
    protected $description = 'Build a single file executable.';

    /**
     * Holds the configuration on is original state.
     *
     * @var null|string
     */
    private static $config;

    /**
     * Holds the box.json on is original state.
     *
     * @var null|string
     */
    private static $box;

    /**
     * Holds the command original output.
     *
     * @var null|\Symfony\Component\Console\Output\OutputInterface
     */
    private $originalOutput;

    /**
     * Makes sure that the `clear` is performed even
     * if the command fails.
     *
     * @return void
     */
    public function __destruct()
    {
        if (null !== self::$config) {
            $this->clear();
        }
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function isEnabled(): bool
    {
        return ! $this->laravel->isProduction();
    }

    /**
     * @throws \JsonException
     */
    public function handle(): void
    {
        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $this->title('Building process');

        $this->build($this->input->getArgument('name') ?? $this->getBinary());
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        return parent::run($input, $this->originalOutput = $output);
    }

    /**
     * Builds the application into a single file.
     *
     * @throws \JsonException
     */
    private function build(string $name): self
    {
        /*
         * We prepare the application for a build, moving it to production. Then,
         * after compile all the code to a single file, we move the built file
         * to the builds folder with the correct permissions.
         */
        $this->prepare()
            ->compile($name)
            ->clear();

        $this->output->writeln(
            sprintf('    Compiled successfully: <fg=green>%s</>', $this->app->buildsPath($name))
        );

        return $this;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @noinspection DisconnectedForeachInstructionInspection
     */
    private function compile(string $name): self
    {
        if (! File::exists($this->app->buildsPath())) {
            File::makeDirectory($this->app->buildsPath());
        }

        // $boxBinary = windows_os() ? '.\box.bat' : './box';
        $boxBinary = realpath(base_path('vendor/laravel-zero/framework/bin/'.(windows_os() ? 'box.bat' : 'box')));

        $process = new Process(
            [$boxBinary, 'compile', '--working-dir='.base_path(), '--config='.base_path('box.json')] + $this->getExtraBoxOptions(),
            null,
            null,
            null,
            $this->getTimeout()
        );

        $section = tap($this->originalOutput->section())->write('');

        $progressBar = tap(
            new ProgressBar(
                $this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL ? new NullOutput() : $section,
                25
            )
        )->setProgressCharacter("\xF0\x9F\x8D\xBA");

        foreach (tap($process)->start() as $type => $data) {
            $progressBar->advance();

            if ($this->option('verbose')) {
                $process::OUT === $type ? $this->info((string) $data) : $this->error((string) $data);
            }
        }

        $progressBar->finish();

        $section->clear();

        $this->task('   2. <fg=yellow>Compile</> into a single file');

        $this->output->newLine();

        File::move($this->app->basePath($this->getBinary()).'.phar', $this->app->buildsPath($name));

        return $this;
    }

    /**
     * @noinspection UsingInclusionReturnValueInspection
     *
     * @throws \JsonException
     */
    private function prepare(): self
    {
        $configFile = $this->app->configPath('app.php');
        self::$config = File::get($configFile);

        $config = include $configFile;

        $config['env'] = 'production';
        $version = $this->option('build-version') ?: $this->ask('Build version?', $config['version']);
        $config['version'] = $version;

        $boxFile = $this->app->basePath('box.json');
        self::$box = File::get($boxFile);

        $this->task(
            '   1. Moving application to <fg=yellow>production mode</>',
            static function () use ($configFile, $config): void {
                File::put($configFile, '<?php return '.var_export($config, true).';'.PHP_EOL);
            }
        );

        $boxContents = json_decode(self::$box, true, 512, JSON_THROW_ON_ERROR);
        $boxContents['main'] = $this->getBinary();

        // Exclude Box binaries in output Phar
        $boxContents['blacklist'] = isset($boxContents['blacklist']) && \is_array($boxContents['blacklist']) ? $boxContents['blacklist'] : [];
        $boxContents['blacklist'][] = 'vendor/laravel-zero/framework/bin/box';
        $boxContents['blacklist'][] = 'vendor/laravel-zero/framework/bin/box.bat';
        $boxContents['blacklist'][] = 'vendor/laravel-zero/framework/bin/box73';
        $boxContents['blacklist'][] = 'vendor/laravel-zero/framework/bin/box73.bat';

        File::put($boxFile, json_encode($boxContents, JSON_THROW_ON_ERROR));

        File::put($configFile, '<?php return '.var_export($config, true).';'.PHP_EOL);

        return $this;
    }

    private function clear(): self
    {
        File::put($this->app->configPath('app.php'), self::$config);

        File::put($this->app->basePath('box.json'), self::$box);

        self::$config = null;

        self::$box = null;

        return $this;
    }

    /**
     * Returns the artisan binary.
     */
    private function getBinary(): string
    {
        return str_replace(["'", '"'], '', Artisan::artisanBinary());
    }

    /**
     * Returns a valid timeout value. Non positive values are converted to null,
     * meaning no timeout.
     *
     * @throws \InvalidArgumentException
     */
    private function getTimeout(): ?float
    {
        if (! is_numeric($this->option('timeout'))) {
            throw new InvalidArgumentException('The timeout value must be a number.');
        }

        $timeout = (float) $this->option('timeout');

        return $timeout > 0 ? $timeout : null;
    }

    /**
     * Enable and listen to async signals for the process.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    private function listenForSignals(): void
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function (): void {
            if (null !== self::$config) {
                $this->clear();
            }

            exit;
        });
    }

    /**
     * Determine if "async" signals are supported.
     */
    private function supportsAsyncSignals(): bool
    {
        return \extension_loaded('pcntl');
    }

    private function getExtraBoxOptions(): array
    {
        $extraBoxOptions = [];

        if ($this->output->isDebug()) {
            $extraBoxOptions[] = '--debug';
        }

        return $extraBoxOptions;
    }
}
