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
use App\Exceptions\UnsupportedConfigFileTypeException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @see https://github.com/hassankhan/config
 */
final class ConfigManager extends Repository implements \JsonSerializable, Arrayable, Jsonable
{
    use Conditionable;
    use Tappable;

    /**
     * @var string
     */
    public const NAME = '.ai-commit.json';

    /**
     * @throws \JsonException
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    public static function load(): void
    {
        resolve('config')->set('ai-commit', self::create());
    }

    /**
     * @throws \JsonException
     */
    public static function create(?array $items = null): self
    {
        if (\is_array($items)) {
            return new self($items);
        }

        return self::createFrom(
            ...array_filter([config_path('ai-commit.php'), self::globalPath(), self::localPath()], 'file_exists')
        );
    }

    /**
     * @throws \JsonException
     */
    public static function createFrom(...$files): self
    {
        return new self(self::readFrom(...$files));
    }

    public static function globalPath(string $path = self::NAME): string
    {
        $path = $path ? \DIRECTORY_SEPARATOR.$path : $path;
        if (windows_os()) {
            return sprintf('C:\\Users\\%s', get_current_user()).$path; // @codeCoverageIgnore
        }

        return exec('cd ~; pwd').$path;
    }

    public static function localPath(string $path = self::NAME): string
    {
        $cwd = getcwd();
        if (false === $cwd) {
            $cwd = realpath('');
        }

        return $cwd.($path ? \DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * @throws \JsonException
     *
     * @return false|int
     */
    public function putGlobal(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    {
        return $this->putFile(self::globalPath(), $options);
    }

    /**
     * @throws \JsonException
     *
     * @return false|int
     */
    public function putLocal(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    {
        return $this->putFile(self::localPath(), $options);
    }

    /**
     * @throws \JsonException
     *
     * @return false|int
     */
    public function putFile(string $file, int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    {
        collect($this->toDotArray())
            ->filter(static function ($val): bool {
                return ! \is_scalar($val) && null !== $val;
            })
            ->keys()
            ->push(
                'generators.openai.parameters.prompt',
                'generators.openai.parameters.user',
                'generators.openaichat.parameters.prompt',
                'generators.openaichat.parameters.user',
            )
            ->unique()
            ->tap(function (Collection $collection): void {
                $this->forget($collection->all());
            });

        return file_put_contents($file, $this->toJson($options));
    }

    /**
     * @throws \JsonException
     */
    public function replaceFrom(string $file): void
    {
        $this->replace(self::readFrom($file));
    }

    public function replace(array $items): void
    {
        $this->items = array_replace_recursive($this->items, $items);
    }

    /**
     * @param array<string>|string $keys
     */
    public function forget($keys): void
    {
        Arr::forget($this->items, $keys);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @throws \JsonException
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_map(static function ($value) {
            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true, 512, JSON_THROW_ON_ERROR);
            }

            if ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->all());
    }

    public function toDotArray(): array
    {
        return Arr::dot($this->toArray());
    }

    public function toArray(): array
    {
        return array_map(static function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->all());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \JsonException
     * @noinspection JsonEncodingApiUsageInspection
     */
    public function toJson($options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @throws \JsonException
     */
    public static function readFrom(...$files): array
    {
        $configurations = array_reduce($files, static function (array $configurations, string $file): array {
            $ext = str(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext->is('php')) {
                $configurations[] = require $file;

                return $configurations;
            }

            if ($ext->is('json')) {
                if (! str($contents = file_get_contents($file))->isJson()) {
                    throw InvalidJsonFileException::make($file);
                }

                $configurations[] = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

                return $configurations;
            }

            throw UnsupportedConfigFileTypeException::make($file);
        }, []);

        return array_replace_recursive(...$configurations);
    }
}
