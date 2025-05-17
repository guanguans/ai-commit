<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

dataset('commit command parameters', [
    [
        /* 'parameters' => */ [],
    ],
    [
        /* 'parameters' => */ [
            '--dry-run' => true,
        ],
    ],
    [
        /* 'parameters' => */ [
            '--diff' => <<<'DIFF'
                tests/Pest.php                        |  1 +
                tests/Unit/ConfigManagerTest.php      |  2 +-
                DIFF,
        ],
    ],
]);
