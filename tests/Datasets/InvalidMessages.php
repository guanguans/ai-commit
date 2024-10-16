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
        <<<'MESSAGE'
            {
              "subject": "fix(app/Generators): update GithubCopilotCliGenerator to include binary command",
              "body": "- Change the command array in the `resolve` function call to include `[\'binary\', \'copilot\', \'explain\', $prompt]` as the command\\n- Update the `mustRun` function
              callback to handle output formatting\\n- Add debug statements to output the generated `$output` variable and perform a `dd()` call\\n- Return the generated `$output` variable"
              }
            MESSAGE,
    ],
    [
        <<<'MESSAGE'

            Welcome to GitHub Copilot in the CLI!
            version 1.0.5 (2024-09-12)

            I'm powered by AI, so surprises and mistakes are possible. Make sure to verify any generated code or suggestions, and share feedback so that we can learn and improve. For more information, see https://gh.io/gh-copilot-transparency


              # Explanation:

              {
              "subject": "fix(app/Commands): fix CommitCommand message parsing",
              "body": "- Update message parsing logic in CommitCommand\n- Trim leading and trailing whitespace\n- Remove control characters from the message"
              }

            MESSAGE,
    ],
]);
