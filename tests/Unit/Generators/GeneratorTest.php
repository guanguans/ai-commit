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

beforeEach(function (): void {
    /** @var \App\Generators\GithubCopilotCliGenerator $generator */
    $generator = app(GeneratorManager::class)->driver('github_copilot_cli');
    $this->generator = $generator;
});

it('can run string cmd', function (): void {
    expect($this->generator->processHelperRun('echo foo'))->isSuccessful()->toBeTrue();
})->group(__DIR__, __FILE__);

it('can sanitize output to JSON', function (): void {
    $output = <<<'OUTPUT'
        {
          "subject": "fix(app/Generators): update GithubCopilotCliGenerator to include binary command",
          "body": "- Change the command array in the `resolve` function call to include `[\'binary\', \'copilot\', \'explain\', $prompt]` as the command\\n- Update the `mustRun` function
          callback to handle output formatting\\n- Add debug statements to output the generated `$output` variable and perform a `dd()` call\\n- Return the generated `$output` variable"
          }
        OUTPUT;

    expect($output)->not->toBeJson()
        ->and($this->generator->sanitizeJson($output))->toBeJson();
})->group(__DIR__, __FILE__);
