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
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

use App\GeneratorManager;
use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function (): void {});

it('throws `ProcessFailedException`', function (): void {
    config('ai-commit')->set('generators.bito_cli.binary', 'bito-cli');
    expect(app(GeneratorManager::class)->driver('bito_cli'))->generate('error');
})->group(__DIR__, __FILE__)->throws(ProcessFailedException::class);
