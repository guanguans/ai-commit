<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

dataset('commit command parameters', [
    [
        /*'parameters' =>*/ [],
    ],
    [
        /*'parameters' =>*/ [
            '--dry-run' => true,
        ],
    ],
    [
        /*'parameters' =>*/ [
            '--diff' => <<<'DIFF'
                tests/Pest.php                        |  1 +
                tests/Unit/ConfigManagerTest.php      |  2 +-
                DIFF,
        ],
    ],
]);
