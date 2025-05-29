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

namespace App;

use App\Exceptions\UnsupportedConfigFileTypeException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use const JSON_THROW_ON_ERROR as JSON_THROW_ON_ERROR1;
use function Illuminate\Filesystem\join_paths;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @see https://github.com/hassankhan/config
 */
final class ConfigManager extends Repository implements \JsonSerializable, \Stringable, Arrayable, Jsonable
{
    use Conditionable;
    use Dumpable;
    use ForwardsCalls;
    use Localizable;
    use Macroable;
    use Tappable;
    public const BASE_NAME = '.ai-commit.json';
    public const BASE_DIRNAME = '.ai-commit';
    public const JSON_OPTIONS = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR;

    /**
     * @throws \JsonException
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function load(): self
    {
        return tap(self::make(), static fn (self $self): null => Config::set('ai-commit', $self));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function make(?array $items = null): self
    {
        if (\is_array($items)) {
            return new self($items);
        }

        return self::makeFrom(...array_filter(
            [config_path('ai-commit.php'), self::globalPath(), self::localPath()],
            file_exists(...)
        ));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function makeFrom(string ...$files): self
    {
        return new self(self::readFrom(...$files));
    }

    public static function globalPath(): string
    {
        return windows_os()
            ? join_paths('C:\\Users', get_current_user(), self::BASE_DIRNAME, self::BASE_NAME)
            : join_paths(exec('cd ~; pwd'), self::BASE_DIRNAME, self::BASE_NAME);
    }

    public static function localPath(string $path = self::BASE_NAME): string
    {
        $cwd = getcwd();

        if (false === $cwd) {
            $cwd = realpath('');
        }

        return join_paths($cwd, $path);
    }

    /**
     * @throws \JsonException
     */
    public function putGlobal(int $options = self::JSON_OPTIONS): bool|int
    {
        return $this->putFile(self::globalPath(), $options);
    }

    /**
     * @throws \JsonException
     */
    public function putLocal(int $options = self::JSON_OPTIONS): bool|int
    {
        return $this->putFile(self::localPath(), $options);
    }

    /**
     * @throws \JsonException
     */
    public function putFile(string $file, int $options = self::JSON_OPTIONS): bool|int
    {
        collect($this->toDotArray())
            ->filter(static function (mixed $value, string $key): bool {
                if (str($key)->is([
                    'generators.*.parameters.messages',
                    'generators.*.parameters.prompt',
                    'generators.*.parameters.user',
                ])) {
                    return true;
                }

                if (!\is_object($value)) {
                    return false;
                }

                foreach (
                    [
                        \JsonSerializable::class,
                        Arrayable::class,
                        Jsonable::class,
                    ] as $class
                ) {
                    if ($value instanceof $class) {
                        return false;
                    }
                }

                return true;
            })
            ->keys()
            ->tap(fn (Collection $keys): self => $this->forget($keys->all()));

        File::ensureDirectoryExists(\dirname($file));

        return File::put($file, $this->toJson($options));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function replaceFrom(string $file): self
    {
        return $this->replace(self::readFrom($file));
    }

    /**
     * @see \Illuminate\Support\Collection::replace()
     */
    public function replace(array $items): self
    {
        $this->items = array_replace_recursive($this->items, $items);

        return $this;
    }

    /**
     * @see \Illuminate\Support\Collection::forget()
     *
     * @param array-key|list<array-key> $keys
     */
    public function forget(mixed $keys): self
    {
        Arr::forget($this->items, $keys);

        return $this;
    }

    /**
     * @see \Illuminate\Support\Collection::jsonSerialize()
     *
     * @throws \JsonException
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_map(
            static function (mixed $value) {
                if ($value instanceof \JsonSerializable) {
                    return $value->jsonSerialize();
                }

                if ($value instanceof Jsonable) {
                    return json_decode($value->toJson(), true, 512, \JSON_THROW_ON_ERROR);
                }

                if ($value instanceof Arrayable) {
                    return $value->toArray();
                }

                return $value;
            },
            $this->all()
        );
    }

    public function toDotArray(): array
    {
        return Arr::dot($this->toArray());
    }

    /**
     * @see \Illuminate\Support\Collection::toArray()
     */
    public function toArray(): array
    {
        return array_map(static fn (mixed $value) => $value instanceof Arrayable ? $value->toArray() : $value, $this->all());
    }

    /**
     * @see \Illuminate\Support\Collection::toJson()
     *
     * {@inheritDoc}
     *
     * @throws \JsonException
     */
    public function toJson(mixed $options = self::JSON_OPTIONS): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private static function readFrom(string ...$files): array
    {
        return array_replace_recursive(
            ...array_reduce(
                $files,
                static function (array $configurations, string $file): array {
                    $configurations[] = match (File::extension($file)) {
                        'php' => require $file,
                        'json' => File::json($file, JSON_THROW_ON_ERROR1),
                        default => throw UnsupportedConfigFileTypeException::make($file)
                    };

                    return $configurations;
                },
                []
            )
        );
    }
}
