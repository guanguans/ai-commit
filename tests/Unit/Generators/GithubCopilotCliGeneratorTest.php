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

use App\GeneratorManager;
use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function (): void {
});

it('throws `ProcessFailedException`', function (): void {
    config('ai-commit')->set('generators.github_copilot_cli.binary', 'github-copilot-cli');
    expect(app(GeneratorManager::class)->driver('github_copilot_cli'))->generate('error');
})->group(__DIR__, __FILE__)->throws(ProcessFailedException::class);

it('can sanitize output to JSON', function (): void {
    $output = <<<'OUTPUT'
        {
          "subject": "fix(app/Generators): update GithubCopilotCliGenerator to include binary command",
          "body": "- Change the command array in the `resolve` function call to include `[\'binary\', \'copilot\', \'explain\', $prompt]` as the command\\n- Update the `mustRun` function
          callback to handle output formatting\\n- Add debug statements to output the generated `$output` variable and perform a `dd()` call\\n- Return the generated `$output` variable"
          }
        OUTPUT;

    expect($output)->not->toBeJson()
        ->and(
            (function (string $output): string {
                return $this->sanitize($output);
            })->call(app(GeneratorManager::class)->driver('github_copilot_cli'), $output)
        )->toBeJson();
})->group(__DIR__, __FILE__);
