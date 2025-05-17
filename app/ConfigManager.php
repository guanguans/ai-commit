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

use App\Exceptions\InvalidJsonFileException;
use App\Exceptions\UnsupportedConfigFileTypeException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
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

    /** @var string */
    public const NAME = '.ai-commit.json';

    /**
     * @throws \JsonException
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @throws \JsonException
     */
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

        return self::createFrom(...array_filter(
            [config_path('ai-commit.php'), self::globalPath(), self::localPath()],
            'file_exists'
        ));
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
            return \sprintf('C:\\Users\\%s\\.ai-commit%s', get_current_user(), $path); // @codeCoverageIgnore
        }

        return \sprintf('%s%s.ai-commit%s', exec('cd ~; pwd'), \DIRECTORY_SEPARATOR, $path);
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
     */
    public function putGlobal(int $options = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE): bool|int
    {
        return $this->putFile(self::globalPath(), $options);
    }

    /**
     * @throws \JsonException
     */
    public function putLocal(int $options = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE): bool|int
    {
        return $this->putFile(self::localPath(), $options);
    }

    /**
     * @throws \JsonException
     */
    public function putFile(string $file, int $options = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE): bool|int
    {
        collect($this->toDotArray())
            ->filter(static fn ($val): bool => !\is_scalar($val) && null !== $val)
            ->keys()
            ->push(
                'generators.openai.parameters.prompt',
                'generators.openai.parameters.user',
                'generators.openai_chat.parameters.prompt',
                'generators.openai_chat.parameters.user',
            )
            ->unique()
            ->tap(function (Collection $collection): void {
                $this->forget($collection->all());
            });

        File::ensureDirectoryExists(\dirname($file));

        return File::put($file, $this->toJson($options));
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
     * @param list<string>|string $keys
     */
    public function forget(array|string $keys): void
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
                return json_decode($value->toJson(), true, 512, \JSON_THROW_ON_ERROR);
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
        return array_map(static fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value, $this->all());
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection JsonEncodingApiUsageInspection
     * @noinspection MissingParameterTypeDeclarationInspection
     *
     * @throws \JsonException
     */
    public function toJson($options = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @throws \JsonException
     */
    public static function readFrom(...$files): array
    {
        $configurations = array_reduce($files, static function (array $configurations, string $file): array {
            $ext = str(pathinfo($file, \PATHINFO_EXTENSION));

            if ($ext->is('php')) {
                $configurations[] = require $file;

                return $configurations;
            }

            if ($ext->is('json')) {
                if (!str($contents = file_get_contents($file))->jsonValidate()) {
                    throw InvalidJsonFileException::make($file);
                }

                $configurations[] = json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);

                return $configurations;
            }

            throw UnsupportedConfigFileTypeException::make($file);
        }, []);

        return array_replace_recursive(...$configurations);
    }
}
