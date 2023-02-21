<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function () {
    config('ai-commit')->set('generators.openai.api_key', 'sk-...');
});

it('argument of path', function () {
    $this->expectException(ProcessFailedException::class);
    $this->expectExceptionMessage('The command "git rev-parse --is-inside-work-tree"');

    $this->artisan(sprintf('commit %s', $this->app->basePath('../')));
});
