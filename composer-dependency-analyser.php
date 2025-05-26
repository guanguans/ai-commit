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

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration)
    ->addPathsToScan(
        [
            __DIR__.'/app/',
            __DIR__.'/bootstrap/',
            __DIR__.'/config/',
            __DIR__.'/resources/',
            __DIR__.'/tests/',
        ],
        false
    )
    ->addPathsToExclude([
        __DIR__.'/tests',
        // __DIR__.'/src/Support/Rectors',
    ])
    ->ignoreUnknownClasses([
        'LaravelZero\Framework\Components\Logo\FigletString',
    ])
    /** @see \ShipMonk\ComposerDependencyAnalyser\Analyser::CORE_EXTENSIONS */
    ->ignoreErrorsOnExtensions(
        [
            // 'ext-pdo',
            // 'ext-pcntl',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            'symfony/console',
            'symfony/var-dumper',
            'guzzlehttp/psr7',
            'illuminate/collections',
            'illuminate/conditionable',
            'illuminate/config',
            'illuminate/console',
            'illuminate/contracts',
            'illuminate/macroable',
            'illuminate/support',
            'laravel-zero/foundation',
            'nunomaduro/laravel-console-summary',
            'psr/log',
            'symfony/process',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            // 'guanguans/ai-commit',
            'guzzlehttp/guzzle',
            'illuminate/http',
            'illuminate/translation',
            'illuminate/validation',
            'laravel-zero/framework',
        ],
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    );
