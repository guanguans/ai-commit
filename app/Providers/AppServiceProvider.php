<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Providers;

use App\ConfigManager;
use App\Contracts\GeneratorContract;
use App\Contracts\OutputAwareContract;
use App\Generators\OpenAIGenerator;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public $singletons = [
        StringableMacro::class => StringableMacro::class,
        StrMacro::class => StrMacro::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ConfigManager::load();
    }

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bindIf(SymfonyStyle::class, function () {
            return new OutputStyle(new ArgvInput(), new ConsoleOutput());
        });

        $this->app->when(OpenAIGenerator::class)
            ->needs('$config')
            ->give($this->app->get('config')->get('ai-commit.generators.openai'));

        $this->app->resolving(OpenAIGenerator::class, static function (GeneratorContract $generator, Application $application): void {
            if ($generator instanceof OutputAwareContract) {
                $generator->setOutput($application->make(SymfonyStyle::class));
            }
        });

        Stringable::mixin($this->app->make(StringableMacro::class));
        Str::mixin($this->app->make(StrMacro::class));
    }
}
