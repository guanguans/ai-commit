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

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Output\ConsoleOutput;

if (!\function_exists('clear_console_screen')) {
    function clear_console_screen(): void
    {
        if (!app()->runningInConsole()) {
            return;
        }

        (new ConsoleOutput)->write("\033\143");
    }
}

if (!\function_exists('str_remove_cntrl')) {
    /**
     * Remove control character.
     */
    function str_remove_cntrl(string $str): string
    {
        return preg_replace('/[[:cntrl:]]/mu', '', $str);
    }
}

if (!\function_exists('validate')) {
    /**
     * Validate the given data with the given rules.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return resolve(Factory::class)->make($data, $rules, $messages, $customAttributes)->validate();
    }
}

if (!\function_exists('str')) {
    /**
     * Get a new stringable object from the given string.
     */
    function str(?string $string = null): Stringable|\Stringable
    {
        if (0 === \func_num_args()) {
            return new class implements \Stringable {
                /**
                 * @noinspection MissingReturnTypeInspection
                 * @noinspection MissingParameterTypeDeclarationInspection
                 */
                public function __call(mixed $method, mixed $parameters)
                {
                    return Str::$method(...$parameters);
                }

                public function __toString(): string
                {
                    return '';
                }
            };
        }

        return Str::of($string);
    }
}

if (!\function_exists('make')) {
    /**
     * @psalm-param string|array<string, mixed> $abstract
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     */
    function make(mixed $abstract, array $parameters = []): mixed
    {
        if (!\is_string($abstract) && !\is_array($abstract)) {
            throw new InvalidArgumentException(\sprintf('Invalid argument type(string/array): %s.', \gettype($abstract)));
        }

        if (\is_string($abstract)) {
            return resolve($abstract, $parameters);
        }

        $classes = ['__class', '_class', 'class'];

        foreach ($classes as $class) {
            if (!isset($abstract[$class])) {
                continue;
            }

            $parameters = Arr::except($abstract, $class) + $parameters;
            $abstract = $abstract[$class];

            return make($abstract, $parameters);
        }

        throw new InvalidArgumentException(\sprintf(
            'The argument of abstract must be an array containing a `%s` element.',
            implode('` or `', $classes)
        ));
    }
}

if (!\function_exists('array_reduce_with_keys')) {
    /**
     * @return null|mixed
     */
    function array_reduce_with_keys(array $array, callable $callback, mixed $carry = null): mixed
    {
        foreach ($array as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }
}

if (!\function_exists('array_map_with_keys')) {
    function array_map_with_keys(callable $callback, array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }
}
