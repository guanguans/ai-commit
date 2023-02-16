<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Unit\Generators;

use App\Generators\OpenAIGenerator;
use Illuminate\Http\Client\RequestException;
use Tests\TestCase;

class OpenAIGeneratorTest extends TestCase
{
    /**
     * @psalm-suppress UnevaluatedCode
     */
    public function testGenerate(): void
    {
        $this->markTestSkipped(__METHOD__);

        $this->expectException(RequestException::class);
        $this->expectExceptionCode(401);

        $openAIGenerator = new OpenAIGenerator(config('ai-commit.generators.openai'));
        $openAIGenerator->generate('foo');
    }
}
