<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Illuminate\Support\Str;

it('will return false', function (): void {
    expect(Str::jsonValidate(''))->toBeFalse();
})->group(__DIR__, __FILE__);
