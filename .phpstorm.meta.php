<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace PHPSTORM_META;

/**
 * PhpStorm Meta file, to provide autocomplete information for PhpStorm.
 */
override(new \Illuminate\Contracts\Container\Container(), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Illuminate\Container\Container::makeWith(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Illuminate\Contracts\Container\Container::get(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Illuminate\Contracts\Container\Container::make(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Illuminate\Contracts\Container\Container::makeWith(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));
override(\App::get(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\App::make(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\App::makeWith(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\app(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));
override(\resolve(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Psr\Container\ContainerInterface::get(0), map([
    '' => '@',
    'Illuminate\Bus\Dispatcher' => \Illuminate\Bus\Dispatcher::class,
    'db' => \Illuminate\Database\DatabaseManager::class,
    'view.finder' => \Illuminate\View\FileViewFinder::class,
]));

override(\Illuminate\Support\Arr::add(0), type(0));
override(\optional(0), type(0));
