<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App;

use App\Exceptions\InvalidJsonFileException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @see https://github.com/hassankhan/config
 */
class ConfigManager extends Repository implements Arrayable, Jsonable, \JsonSerializable
{
    public const NAME = '.ai-commit.json';

    final public static function load(): self
    {
        $self = self::create();

        resolve('config')->set('ai-commit', $self);

        return $self;
    }

    public static function create(?array $items = null): self
    {
        if (is_array($items)) {
            return new self($items);
        }

        $files = [
            config_path('ai-commit.php'),
            self::globalPath(),
            self::cwdPath(),
        ];

        return self::createFrom(...array_filter($files, 'file_exists'));
    }

    public static function createFrom(...$files): self
    {
        $config = array_reduce($files, function (array $items, string $file): array {
            $ext = str(pathinfo($file, PATHINFO_EXTENSION));

            if ($ext->is('php')) {
                $items[] = require $file;

                return $items;
            }

            if ($ext->is('json')) {
                $config = json_decode(file_get_contents($file), true);
                if (JSON_ERROR_NONE !== json_last_error()) {
                    throw InvalidJsonFileException::make($file);
                }

                $items[] = $config;

                return $items;
            }

            throw new \InvalidArgumentException("Invalid argument type: `$ext`.");
        }, []);

        return new self(array_replace_recursive(...$config));
    }

    public static function globalPath(string $path = self::NAME): string
    {
        if (windows_os()) {
            return sprintf('C:\\Users\\%s', get_current_user()).$path;
        }

        return exec('cd ~; pwd').$path;
    }

    public static function cwdPath(string $path = self::NAME): string
    {
        $cwd = getcwd();
        if (false === $cwd) {
            $cwd = realpath('');
        }

        return $cwd.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * @return $this
     */
    public function merge(array $items): self
    {
        $this->items = array_replace_recursive($this->items, $items);

        return $this;
    }

    /**
     * @return $this
     */
    public function forget($keys): self
    {
        Arr::forget($this->items, $keys);

        return $this;
    }

    /**
     * Collect the values into a collection.
     *
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function collect(): Collection
    {
        return new Collection($this->all());
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_map(function ($value) {
            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            }

            if ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->all());
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function toGlobal(int $options = JSON_PRETTY_PRINT)
    {
        $this->toFile(self::globalPath(), $options);
    }

    public function toCwd(int $options = JSON_PRETTY_PRINT)
    {
        $this->toFile(self::cwdPath(), $options);
    }

    public function toFile(string $file, int $options = 0)
    {
        return file_put_contents($file, $this->toJson($options));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
