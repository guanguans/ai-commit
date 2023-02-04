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
use Illuminate\Support\Collection;
use Iterator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @see https://github.com/hassankhan/config
 */
class ConfigManager extends Repository implements Arrayable, Jsonable, \JsonSerializable, \Stringable, \Iterator
{
    final public static function load(): void
    {
        resolve('config')->set('ai-commit', self::create());
    }

    final public static function create(): self
    {
        $files = [
            config_path('ai-commit.php'),
            windows_os() ? sprintf('C:\\Users\\%s\\.ai-commit.json', get_current_user()) : sprintf('%s/.ai-commit.json', exec('cd ~; pwd')),
            getcwd().DIRECTORY_SEPARATOR.'.ai-commit.json',
        ];

        return static::createFrom(...array_filter($files, 'file_exists'));
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

        return new static(array_replace_recursive(...$config));
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
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
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

    /**
     * Convert the collection to its string representation.
     *
     * @noinspection DebugFunctionUsageInspection
     */
    public function toString(string $type = 'json'): string
    {
        if (str($type)->is('json')) {
            return $this->toJson(JSON_PRETTY_PRINT);
        }

        if (str($type)->is('php')) {
            return var_export($this->toArray(), true);
        }

        throw new \InvalidArgumentException("Invalid argument type: `$type`.");
    }

    public function toCwd()
    {
        $file = getcwd().DIRECTORY_SEPARATOR.'.ai-commit.json';

        return $this->toFile($file);
    }

    public function toGlobal()
    {
        $file = windows_os() ? sprintf('C:\\Users\\%s\\.ai-commit.json', get_current_user()) : sprintf('%s/.ai-commit.json', exec('cd ~; pwd'));

        return $this->toFile($file);
    }

    public function toFile(string $file)
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);

        return file_put_contents($file, $this->toString($type));
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Iterator Methods.
     */

    /**
     * Returns the data array element referenced by its internal cursor.
     *
     * @return mixed The element referenced by the data array's internal cursor.
     *               If the array is empty or there is no element at the cursor, the
     *               function returns false. If the array is undefined, the function
     *               returns null
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return is_array($this->items) ? current($this->items) : null;
    }

    /**
     * Returns the data array index referenced by its internal cursor.
     *
     * @return mixed The index referenced by the data array's internal cursor.
     *               If the array is empty or undefined or there is no element at the
     *               cursor, the function returns null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return is_array($this->items) ? key($this->items) : null;
    }

    /**
     * Moves the data array's internal cursor forward one element.
     *
     * @return mixed The element referenced by the data array's internal cursor
     *               after the move is completed. If there are no more elements in the
     *               array after the move, the function returns false. If the data array
     *               is undefined, the function returns null
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        return is_array($this->items) ? next($this->items) : null;
    }

    /**
     * Moves the data array's internal cursor to the first element.
     *
     * @return mixed The element referenced by the data array's internal cursor
     *               after the move is completed. If the data array is empty, the function
     *               returns false. If the data array is undefined, the function returns
     *               null
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        return is_array($this->items) ? reset($this->items) : null;
    }

    /**
     * Tests whether the iterator's current index is valid.
     *
     * @return bool True if the current index is valid; false otherwise
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return is_array($this->items) ? null !== key($this->items) : false;
    }

    /**
     * Remove a value using the offset as a key.
     *
     * @param string $key
     *
     * @return void
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
    }
}
