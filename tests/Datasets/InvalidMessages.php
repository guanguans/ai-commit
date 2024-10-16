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

dataset('invalid messages', [
    [
        'message' => <<<'MESSAGE'
            {
              "subject": "fix(app/Generators): update GithubCopilotCliGenerator to include binary command",
              "body": "- Change the command array in the `resolve` function call to include `[\'binary\', \'copilot\', \'explain\', $prompt]` as the command\\n- Update the `mustRun` function
              callback to handle output formatting\\n- Add debug statements to output the generated `$output` variable and perform a `dd()` call\\n- Return the generated `$output` variable"
              }
            MESSAGE,
    ],
]);
