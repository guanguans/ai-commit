<?php

/** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Exceptions\Handler;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Output\OutputInterface;

it('can render exception for console', function () {
    $this->app['env'] = 'production';
    $output = Mockery::spy(OutputInterface::class);
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    expect($this->app->get(Handler::class))
        ->renderForConsole(
            $output,
            Mockery::spy(HttpClientException::class)
        )->toBeNull()
        ->renderForConsole(
            $output,
            new ValidationException(
                $this->app->get(Factory::class)->make(
                    ['foo' => 'bar'],
                    ['foo' => 'int']
                )
            )
        )->toBeNull()
        ->renderForConsole(
            $output,
            Mockery::spy(\Exception::class)
        )->toBeNull();
})->group(__DIR__, __FILE__);

it('can report exception', function () {
    $this->app['env'] = 'testing';
    $exception = new \Exception('foo');
    $handler = $this->app->get(Handler::class);
    expect($handler)->shouldReport($exception)->toBeTrue();

    $this->app['env'] = 'production';
    expect($handler)->shouldReport($exception)->toBeFalse();
})->group(__DIR__, __FILE__);
