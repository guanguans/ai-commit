<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Commands\ThanksCommand;

it('can thank the user for using this tool', function (): void {
    mockExecFunction();

    $this->artisan(ThanksCommand::class)
        ->expectsQuestion('Can you quickly <options=bold>star our GitHub repository</>? 🙏🏻', 'yes')
        ->assertSuccessful();
})->group(__DIR__, __FILE__);

// Helper Functions
/**
 * Mocks the `exec` function to simulate command execution for the ThanksCommand.
 */
function mockExecFunction(): void {
    $this->getFunctionMock(class_namespace(ThanksCommand::class), 'exec')
        ->expects($this->once())
        ->willReturn('');
}
