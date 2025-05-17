<?php

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
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use phpmock\phpunit\PHPMock;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use PHPMock;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void {}

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void {}

    /**
     * This method is called before each test.
     *
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $configManager = ConfigManager::createFrom($this->app->configPath('ai-commit.php'));
        $configManager->set('generators.openai.api_key', 'sk-...');
        $configManager->set('generators.bito_cli.path', 'bito-cli-path...');

        config()->set('ai-commit', $configManager);
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->finish();
        \Mockery::close();
    }

    /**
     * Run extra tear down code.
     */
    protected function finish(): void
    {
        // call more tear down methods
    }
}
