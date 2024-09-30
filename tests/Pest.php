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
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

uses(TestCase::class)
    ->beforeAll(function (): void {
    })
    ->beforeEach(function (): void {
        // setup_http_fake();
        config()->set('app.version', 'v'.config('app.version'));
    })
    ->afterEach(function (): void {
    })
    ->afterAll(function (): void {
    })
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeTwo', function () {
    return $this->toBe(2);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * @param object|string $class
 *
 * @throws \ReflectionException
 */
function class_namespace($class): string
{
    $class = is_object($class) ? get_class($class) : $class;

    return (new ReflectionClass($class))->getNamespaceName();
}

function repository_path(string $path = ''): string
{
    return fixtures_path('repository'.($path ? DIRECTORY_SEPARATOR.$path : $path));
}

function fixtures_path(string $path = ''): string
{
    return __DIR__.'/Fixtures'.($path ? DIRECTORY_SEPARATOR.$path : $path);
}

/**
 * @psalm-suppress UnusedClosureParam
 */
function setup_http_fake(): void
{
    Http::fake([
        '*://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/*' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['messages'][0]['content'];
            $status = array_flip(Response::$statusTexts)[$prompt] ?? 200;
            $body = $status >= 400
                ? <<<'json'
                    {"error_code":17,"error_msg":"Open api daily request limit reached"}
                    json

                : <<<'json'
                    {"id":"as-rx9g6c5sqp","object":"chat.completion","created":1692253331,"sentence_id":3,"is_end":false,"is_truncated":false,"result":"PHP的主要目标是允许网络开发人P也被用于其他很多领域。","need_clear_history":false,"usage":{"prompt_tokens":4,"completion_tokens":35,"total_tokens":122}}
                    json;

            return Http::response($body);
        },
        '*://aip.baidubce.com/oauth/2.0/token?*' => function (Request $request, array $options): PromiseInterface {
            return Http::response(
                <<<'json'
                    {"refresh_token":"25.52df6887dac3b388c94b78854d231.315360000.2007686387.282335-37780661","expires_in":2592000,"session_key":"dWr0t8VVzq5EZZUS0QyCERVJZzIVFJ9YQoDEEtzXuoFUCQ9gpDzNYinxjAt5vlLom+7QYYlZwfE89gyj6ePr9ohVeuw==","access_token":"24.6a024ba0cf6c1fd210fb1d2e7251b.2592000.1694918387.282335-37780661","scope":"public brain_all_scope ai_custom_yiyan_com ai_custom_yiyan_com_eb_instant wenxinworkshop_mgr ai_custom_yiyan_com_bloomz7b1 ai_custom_yiyan_com_emb_text wise_adapt lebo_resource_base lightservice_public hetu_basic lightcms_map_poi kaidian_kaidian ApsMisTest_Test权限 vis-classify_flower lpq_开放 cop_helloScope ApsMis_fangdi_permission smartapp_snsapi_base smartapp_mapp_dev_manage iop_autocar oauth_tp_app smartapp_smart_game_openapi oauth_sessionkey smartapp_swanid_verify smartapp_opensource_openapi smartapp_opensource_recapi fake_face_detect_开放Scope vis-ocr_虚拟人物助理 idl-video_虚拟人物助理 smartapp_component smartapp_search_plugin avatar_video_test b2b_tp_openapi b2b_tp_openapi_online smartapp_gov_aladin_to_xcx","session_secret":"940b2b2ad62ceb6dd33fc03468def"}
                    json
            );
        },
    ]);

    Http::fake([
        '*://api.moonshot.cn/v1/chat/completions' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['messages'][0]['content'];
            $status = array_flip(Response::$statusTexts)[$prompt] ?? 200;
            $body = $status >= 400
                ? <<<'json'
                    {"error":{"message":"auth failed","type":"invalid_authentication_error"}}
                    json

                : <<<'json'
                    {"id":"cmpl-64d18ab895224e74b9c78a9a8c233585","object":"chat.completion","created":3943160,"model":"moonshot-v1-8k","choices":[{"index":0,"message":{"role":"assistant","content":"你好，李雷！1+1等于2。如果你有更复杂的数学问题或者其他问题，也可以随时问我。"},"finish_reason":"stop"}],"usage":{"prompt_tokens":83,"completion_tokens":25,"total_tokens":108}}
                    json;

            return Http::response($body, $status);
        },
        '*://api.moonshot.cn/v1/models' => function (Request $request, array $options): PromiseInterface {
            return Http::response(
                <<<'json'
                    {"object":"list","data":[{"created":1712151494,"id":"moonshot-v1-8k","object":"model","owned_by":"moonshot","permission":[{"created":0,"id":"","object":"","allow_create_engine":false,"allow_sampling":false,"allow_logprobs":false,"allow_search_indices":false,"allow_view":false,"allow_fine_tuning":false,"organization":"public","group":"public","is_blocking":false}],"root":"","parent":""},{"created":1712151494,"id":"moonshot-v1-32k","object":"model","owned_by":"moonshot","permission":[{"created":0,"id":"","object":"","allow_create_engine":false,"allow_sampling":false,"allow_logprobs":false,"allow_search_indices":false,"allow_view":false,"allow_fine_tuning":false,"organization":"public","group":"public","is_blocking":false}],"root":"","parent":""},{"created":1712151494,"id":"moonshot-v1-128k","object":"model","owned_by":"moonshot","permission":[{"created":0,"id":"","object":"","allow_create_engine":false,"allow_sampling":false,"allow_logprobs":false,"allow_search_indices":false,"allow_view":false,"allow_fine_tuning":false,"organization":"public","group":"public","is_blocking":false}],"root":"","parent":""}]}
                    json
            );
        },
    ]);

    Http::fake([
        '*://api.openai.com/v1/completions' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['prompt'];
            $status = array_flip(Response::$statusTexts)[$prompt] ?? 200;
            $text = transform($prompt, function ($prompt): string {
                switch ($prompt) {
                    case 'empty':
                        $text = '';

                        break;
                    case 'invalid':
                        $text = <<<'json'
                            "subject":"Fix(OpenAIGenerator): Debugging output","body":"- Add var_dump() for debugging output- Add var_dump() for stream response"}
                            json;

                        break;
                    default:
                        $text = <<<'json'
                            {"subject":"Fix(OpenAIGenerator): Debugging output","body":"- Add var_dump() for debugging output- Add var_dump() for stream response"}
                            json;

                        break;
                }

                return $text;
            });

            $body = $status >= 400
                ? <<<'json'
                    {"error":{"message":"Incorrect API key provided: sk-........ You can find your API key at https:\/\/platform.openai.com\/account\/api-keys.","type":"invalid_request_error","param":null,"code":"invalid_api_key"}}
                    json
                : [
                    'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
                    'object' => 'text_completion',
                    'created' => 1677143178,
                    'model' => 'text-davinci-003',
                    'choices' => [
                        [
                            'text' => $text,
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
        '*://api.openai.com/v1/chat/completions' => function (Request $request, array $options): PromiseInterface {
            $prompt = $options['laravel_data']['messages'][0]['content'];
            $status = array_flip(Response::$statusTexts)[$prompt] ?? 200;
            $body = $status >= 400
                ? <<<'json'
                    {"error":{"message":"Incorrect API key provided: sk-........ You can find your API key at https:\/\/platform.openai.com\/account\/api-keys.","type":"invalid_request_error","param":null,"code":"invalid_api_key"}}
                    json

                : <<<'json'
                    {"id":"chatcmpl-6pqDoRwRGQAlRvJnesR9QMG9rxpyK","object":"chat.completion","created":1677813488,"model":"gpt-3.5-turbo-0301","usage":{"prompt_tokens":8,"completion_tokens":16,"total_tokens":24},"choices":[{"delta":{"role":"assistant","content":"PHP (Hypertext Preprocessor) is a server-side scripting language used"},"finish_reason":"length","index":0}]}
                    json;

            return Http::response($body, $status);
        },
        '*://api.openai.com/v1/models' => function (Request $request, array $options): PromiseInterface {
            return Http::response(
                <<<'json'
                    {"object":"list","data":[{"id":"babbage","object":"model","created":1649358449,"owned_by":"openai","permission":[{"id":"modelperm-49FUp5v084tBB49tC4z8LPH5","object":"model_permission","created":1669085501,"allow_create_engine":false,"allow_sampling":true,"allow_logprobs":true,"allow_search_indices":false,"allow_view":true,"allow_fine_tuning":false,"organization":"*","group":null,"is_blocking":false}],"root":"babbage","parent":null}]}
                    json
            );
        },
    ]);
}

function reset_http_fake(?Factory $factory = null): void
{
    (function (): void {
        $this->stubCallbacks = collect();
    })->call($factory ?? Http::getFacadeRoot());
}
