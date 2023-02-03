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
use App\GeneratorManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public $singletons = [
        GeneratorManager::class => GeneratorManager::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ConfigManager::load();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
