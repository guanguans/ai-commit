<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use App\ConfigManager;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use phpmock\phpunit\PHPMock;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use PHPMock;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * This method is called before each test.
     *
     * @psalm-suppress UnusedClosureParam
     */
    protected function setUp(): void
    {
        parent::setUp();

        $configManager = ConfigManager::createFrom($this->app->configPath('ai-commit.php'));
        $configManager->set('generators.openai.api_key', 'sk-...');
        config()->set('ai-commit', $configManager);
        $this->setUpHttpFake();
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    protected function setUpHttpFake()
    {
        Http::fake([
            '*://api.openai.com/v1/*' => function (Request $request, array $options) {
                $status = transform($options['laravel_data']['prompt'], function ($prompt) {
                    return array_flip(Response::$statusTexts)[$prompt] ?? 200;
                });

                $body = $status >= 400 ?
                    [
                        'error' => [
                            'message' => 'Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.',
                            'type' => 'invalid_request_error',
                            'param' => null,
                            'code' => 'invalid_api_key',
                        ],
                    ] :
                    [
                        'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
                        'object' => 'text_completion',
                        'created' => 1677143178,
                        'model' => 'text-davinci-003',
                        'choices' => [
                            0 => [
                                'text' => '[    {        "id": 1,        "subject": "Fix(OpenAIGenerator): Debugging output",        "body": "- Add var_dump() for debugging output- Add var_dump() for stream response"    },    {        "id": 2,        "subject": "Refactor(OpenAIGenerator): Error handling",        "body": "- Check for error response in JSON- Handle error response"    },    {        "id": 3,        "subject": "Docs(OpenAIGenerator): Update documentation",        "body": "- Update documentation for OpenAIGenerator class"    }]',
                                'index' => 0,
                                'logprobs' => null,
                                'finish_reason' => 'stop',
                            ],
                        ],
                        'usage' => [
                            'prompt_tokens' => 749,
                            'completion_tokens' => 159,
                            'total_tokens' => 908,
                        ],
                    ];

                return Http::response($body, $status);
            },
        ]);
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
