<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/ai-commit
 */

namespace App\Clients;

use App\Listeners\DefineTraceIdListener;
use Composer\InstalledVersions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Support\Traits\Tappable;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \Illuminate\Support\Collection $stubCallbacks
 *
 * @mixin \Illuminate\Http\Client\PendingRequest
 */
abstract class AbstractClient
{
    // use Conditionable;
    use Dumpable;
    use ForwardsCalls;
    use Localizable;
    use Tappable;
    protected readonly Repository $configRepository;
    private ?string $userAgent = null;
    private readonly PendingRequest $pendingRequest;

    public function __construct(array $config)
    {
        $this->configRepository = new Repository($this->validateConfig($config));
        $this->pendingRequest = $this->extendPendingRequest($this->defaultPendingRequest());
    }

    /**
     * @see \Illuminate\Http\Client\Factory::__call()
     *
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     *
     * @return mixed|PendingRequest|static
     */
    public function __call(string $name, array $arguments): mixed
    {
        $result = $this->forwardCallTo($this->pendingRequest(), $name, $arguments);

        if ($result === $this->pendingRequest) {
            return $this;
        }

        return $result;
    }

    public static function sanitizeData(string $data): string
    {
        return (string) str($data)->whenStartsWith(
            $prefix = 'data: ',
            static fn (Stringable $data): Stringable => $data->after($prefix)
        );
    }

    public function ddPendingRequest(mixed ...$args): static
    {
        $this->pendingRequest()->dd(...$args);

        return $this;
    }

    public function dumpPendingRequest(mixed ...$args): static
    {
        $this->pendingRequest()->dump(...$args);

        return $this;
    }

    public function clonePendingRequest(?callable $callback = null): PendingRequest
    {
        return $this->pendingRequest($callback, true);
    }

    /**
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function pendingRequest(?callable $callback = null, bool $clone = false): PendingRequest
    {
        return tap(
            tap(
                $clone ? clone $this->pendingRequest : $this->pendingRequest,
                function (PendingRequest $pendingRequest): void {
                    /** @see \Illuminate\Http\Client\Factory::createPendingRequest() */
                    $pendingRequest
                        /** @phpstan-ignore-next-line */
                        ->stub((fn (): Collection => $this->stubCallbacks)->call(Http::getFacadeRoot()))
                        ->preventStrayRequests(Http::preventingStrayRequests());
                }
            ),
            $callback ?? static fn (): null => null
        );
    }

    abstract protected function configRules(): array;

    abstract protected function extendPendingRequest(PendingRequest $pendingRequest): PendingRequest;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }

    protected function requestId(): ?string
    {
        return \defined('TRACE_ID') ? TRACE_ID : null;
    }

    /**
     * @noinspection OffsetOperationsInspection
     *
     * @return array<string, scalar>
     */
    protected function userAgentItems(): array
    {
        return [
            'ai-commit' => str(config('app.version'))->whenStartsWith('v', static fn (Stringable $version): Stringable => $version->replaceFirst('v', '')),
            'laravel' => InstalledVersions::getPrettyVersion('illuminate/support'),
            'guzzle' => InstalledVersions::getPrettyVersion('guzzlehttp/guzzle'),
            'curl' => (curl_version() ?: ['version' => 'unknown'])['version'],
            'PHP' => \PHP_VERSION,
            \PHP_OS => php_uname('r'),
        ];
    }

    protected function configMessages(): array
    {
        return [];
    }

    protected function configAttributes(): array
    {
        return [];
    }

    /**
     * @param int $retries 重试次数
     * @param int $baseIntervalMs 基础间隔（毫秒）
     *
     * @see \GuzzleHttp\RetryMiddleware::exponentialDelay()
     * @see \retry()
     */
    protected function fibonacciRetryIntervals(int $retries, int $baseIntervalMs = 1000): array
    {
        $intervals = [];
        $prev = 0;
        $curr = 1;

        for ($index = 0; $index < $retries; ++$index) {
            $intervals[] = $curr * $baseIntervalMs;
            [$prev, $curr] = [$curr, $prev + $curr];
        }

        return $intervals;
    }

    private function defaultPendingRequest(): PendingRequest
    {
        return Http::when(
            $this->configRepository->get('base_url'),
            static fn (
                PendingRequest $pendingRequest,
                string $baseUrl
            ) => $pendingRequest->baseUrl($baseUrl)
        )
            ->when(
                $this->getUserAgent(),
                static fn (
                    PendingRequest $pendingRequest,
                    string $userAgent
                ) => $pendingRequest->withUserAgent($userAgent)
            )
            ->withOptions(config()->array('ai-commit.http_options'))
            ->withOptions($this->configRepository->get('http_options'))
            ->retry(
                times: $this->configRepository->get('retry.times'),
                sleepMilliseconds: $this->configRepository->get('retry.sleep'),
                when: $this->configRepository->get('retry.when'),
                throw: $this->configRepository->get('retry.throw')
            )
            ->when(
                $this->requestId(),
                static fn (
                    PendingRequest $pendingRequest,
                    string $requestId
                ) => $pendingRequest->withHeader(DefineTraceIdListener::X_REQUEST_ID, $requestId)
            )
            ->withMiddleware(Middleware::mapRequest(
                static fn (RequestInterface $request): MessageInterface => $request->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ))
            ->withMiddleware($this->makeLoggerMiddleware($this->configRepository->get('logger')))
            ->withMiddleware(Middleware::mapResponse(
                static fn (ResponseInterface $response): MessageInterface => $response->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ))
            ->when(
                $this->requestId(),
                static fn (PendingRequest $pendingRequest, string $requestId) => $pendingRequest->withMiddleware(
                    Middleware::mapResponse(
                        static fn (ResponseInterface $response): ResponseInterface => $response->withHeader(DefineTraceIdListener::X_REQUEST_ID, $requestId)
                    )
                )
            );
    }

    private function validateConfig(array $config): array
    {
        return $this->validate(
            array_replace_recursive($this->defaultConfig(), $config),
            $this->configRules() + $this->defaultConfigRules(),
            $this->configMessages(),
            $this->configAttributes()
        );
    }

    private function defaultConfig(): array
    {
        return [
            // 'base_url' => null,
            'logger' => 'null',
            'http_options' => [
                // RequestOptions::CONNECT_TIMEOUT => 10,
                // RequestOptions::TIMEOUT => 30,
            ],
            /**
             * @see PendingRequest::retry()
             * @see PendingRequest::$tries
             */
            'retry' => [
                'times' => $this->fibonacciRetryIntervals(1),
                'sleep' => 1000,
                // 'when' => static fn (\Throwable $throwable): bool => $throwable instanceof ConnectException,
                'when' => null,
                'throw' => true,
            ],
        ];
    }

    private function defaultConfigRules(): array
    {
        return [
            'base_url' => 'string',
            'logger' => 'nullable|string',
            'http_options' => 'array',
            'retry' => 'array',
        ];
    }

    private function getUserAgent(): string
    {
        return $this->userAgent ??= collect($this->userAgentItems())
            ->map(static fn (mixed $value, string $name): string => "$name/$value")
            ->implode(' ');
    }

    private function makeLoggerMiddleware(?string $logger = null): callable
    {
        return Middleware::log(Log::channel($logger), new MessageFormatter(MessageFormatter::DEBUG));
    }
}
