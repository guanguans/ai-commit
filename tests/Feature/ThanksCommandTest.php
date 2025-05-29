<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnused */
/** @noinspection SqlResolve */
declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\Commands\ThanksCommand;

it('can thanks for using this tool', function (): void {
    $this->getFunctionMock(class_namespace(ThanksCommand::class), 'exec')
        ->expects($this->once())
        ->willReturn('');

    $this->artisan(ThanksCommand::class)
        ->expectsQuestion('Can you quickly <options=bold>star our GitHub repository</>? 🙏🏻', 'yes')
        ->assertSuccessful();
})->group(__DIR__, __FILE__);
