<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnusedAliasInspection */
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

namespace Tests;

use App\ConfigManager;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\phpunit\PHPMock;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

abstract class TestCase extends BaseTestCase
{
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    // use LazilyRefreshDatabase;
    // use RefreshDatabase;

    use MockeryPHPUnitIntegration;
    use PHPMock;
    use VarDumperTestTrait;
    protected bool $seed = false;

    protected function setUp(): void
    {
        parent::setUp();
        // \DG\BypassFinals::enable();
        $this->startMockery();

        $configManager = ConfigManager::createFrom($this->app->configPath('ai-commit.php'));
        $configManager->set('generators.openai.api_key', 'sk-...');
        $configManager->set('generators.bito_cli.path', 'bito-cli-path...');

        config()->set('ai-commit', $configManager);
    }

    protected function tearDown(): void
    {
        $this->finish();
        $this->closeMockery();
        parent::tearDown();
    }

    private function finish(): void {}
}
