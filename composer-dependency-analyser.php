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
            __DIR__.'/config',
            __DIR__.'/src',
        ],
        false
    )
    ->addPathsToExclude([
        __DIR__.'/tests',
        // __DIR__.'/src/Support/Rectors',
    ])
    /** @see \ShipMonk\ComposerDependencyAnalyser\Analyser::CORE_EXTENSIONS */
    ->ignoreErrorsOnExtensions(
        [
            // 'ext-pdo',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            'nesbot/carbon',
            'symfony/console',
            'symfony/http-foundation',
            'symfony/var-dumper',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackageAndPath(
        'maximebf/debugbar',
        __DIR__.'/src/Outputs/DebugBarOutput.php',
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackagesAndPaths(
        [
            'barryvdh/laravel-debugbar',
            // 'php-debugbar/php-debugbar',
        ],
        [__DIR__.'/src/Outputs/DebugBarOutput.php'],
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    )
    ->ignoreErrorsOnPackageAndPath(
        'laradumps/laradumps-core',
        __DIR__.'/src/Outputs/LaraDumpsOutput.php',
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackageAndPath(
        'spatie/ray',
        __DIR__.'/src/Outputs/RayOutput.php',
        [ErrorType::SHADOW_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            // 'guanguans/ai-commit',
        ],
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    );
