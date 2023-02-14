<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Feature;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Tests\TestCase;

class CommitCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config('ai-commit')->set('generators.openai.api_key', 'sk-...');
    }

    public function testArgumentOfPath(): void
    {
        $this->expectException(ProcessFailedException::class);
        $this->expectExceptionMessage('The command "git rev-parse --is-inside-work-tree"');

        $this->artisan(sprintf('commit %s', $this->app->basePath('../')));
    }
}
