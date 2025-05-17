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

namespace App\Support;

use Composer\InstalledVersions;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @property $stubCallbacks
 */
abstract class FoundationSDK
{
    use Conditionable;
    use Macroable;
    use Tappable;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \Illuminate\Http\Client\Factory
     */
    protected $http;

    /**
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected $defaultPendingRequest;

    public function __construct(array $config)
    {
        $this->config = $this->validateConfig($config);
        $this->http = Http::getFacadeRoot();
        $this->defaultPendingRequest = $this->buildDefaultPendingRequest($this->config);
    }

    public static function sanitizeData(string $data): string
    {
        return (string) str($data)->whenStartsWith(
            $prefix = 'data: ',
            static function (Stringable $data) use ($prefix): Stringable {
                return $data->after($prefix);
            }
        );
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function ddRequestData(): self
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']); // @codeCoverageIgnore
                exit(1); // @codeCoverageIgnore
            });
        });
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function dumpRequestData(): self
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']); // @codeCoverageIgnore
            });
        });
    }

    public function buildLogMiddleware(?LoggerInterface $logger = null, ?MessageFormatter $formatter = null, string $logLevel = 'info'): callable
    {
        $logger = $logger ?: Log::channel('daily');
        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    public function tapDefaultPendingRequest(callable $callback): self
    {
        $this->defaultPendingRequest = tap($this->defaultPendingRequest, $callback);

        return $this;
    }

    /**
     * @psalm-suppress UndefinedThisPropertyFetch
     */
    public function cloneDefaultPendingRequest(): PendingRequest
    {
        return tap(clone $this->defaultPendingRequest, function (PendingRequest $request): void {
            $getStubCallbacks = function (): Collection {
                return $this->stubCallbacks;
            };

            $request->stub($getStubCallbacks->call($this->http));
        });
    }

    /**
     * ```php
     * protected function validateConfig(array $config): array
     * {
     *     return validate($config, [
     *         'http_options' => 'array',
     *     ]);
     * }
     * ```.
     *
     * @throws \Illuminate\Validation\ValidationException laravel validation rules
     *
     * @return array The merged and validated options
     */
    abstract protected function validateConfig(array $config): array;

    /**
     * ```php
     * protected function buildPendingRequest(array $config): PendingRequest
     * {
     *     return Http::withOptions($config['http_options'])
     *         ->baseUrl($config['baseUrl'])
     *         ->asJson()
     *         ->withMiddleware($this->buildLogMiddleware());
     * }
     * ```.
     */
    protected function buildDefaultPendingRequest(array $config): PendingRequest
    {
        return $this->http
            ->withUserAgent(self::userAgent())
            ->withOptions((array) config('ai-commit.http_options'));
    }

    protected static function userAgent(): string
    {
        static $userAgent;

        if (null === $userAgent) {
            $userAgent = implode(' ', [
                sprintf(
                    'ai-commit/%s',
                    str(config('app.version'))->whenStartsWith('v', static function (Stringable $version): Stringable {
                        return $version->replaceFirst('v', '');
                    })
                ),
                sprintf('guzzle/%s', InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')),
                sprintf('curl/%s', curl_version()['version']),
                sprintf('PHP/%s', PHP_VERSION),
                sprintf('%s/%s', PHP_OS, php_uname('r')),
            ]);
        }

        return $userAgent;
    }
}
