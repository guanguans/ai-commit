<?php

declare(strict_types=1);

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
use JsonException;

final class ConfigManager extends Repository implements \JsonSerializable, Arrayable, Jsonable
{
    use Conditionable;
    use Tappable;

    public const NAME = '.ai-commit.json';

    public function __toString(): string
    {
        return $this->toJson();
    }

    public static function load(): void
    {
        config(['ai-commit' => self::create()->all()]);
    }

    public static function create(?array $items = null): self
    {
        if ($items) {
            return new self($items);
        }

        return self::createFrom(...array_filter([
            config_path('ai-commit.php'), 
            self::globalPath(), 
            self::localPath()
        ], 'file_exists'));
    }

    public static function createFrom(...$files): self
    {
        return new self(self::readFrom(...$files));
    }

    public static function globalPath(string $path = self::NAME): string
    {
        $path = $path ? DIRECTORY_SEPARATOR . $path : '';
        return windows_os() 
            ? sprintf('C:\\Users\\%s\\.ai-commit%s', get_current_user(), $path)
            : sprintf('%s%s.ai-commit%s', exec('cd ~; pwd'), DIRECTORY_SEPARATOR, $path);
    }

    public static function localPath(string $path = self::NAME): string
    {
        $cwd = getcwd() ?: realpath('');
        return $cwd . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function putGlobal(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): bool|int
    {
        return $this->putFile(self::globalPath(), $options);
    }

    public function putLocal(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): bool|int
    {
        return $this->putFile(self::localPath(), $options);
    }

    public function putFile(string $file, int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): bool|int
    {
        collect($this->toDotArray())
            ->filter(fn ($val) => is_scalar($val) && $val !== null)
            ->keys()
            ->tap(fn (Collection $collection) => $this->forget($collection->all()));

        File::ensureDirectoryExists(dirname($file));
        return File::put($file, $this->toJson($options));
    }

    public function replaceFrom(string $file): void
    {
        $this->replace(self::readFrom($file));
    }

    public function replace(array $items): void
    {
        $this->items = array_replace_recursive($this->items, $items);
    }

    public function forget($keys): void
    {
        Arr::forget($this->items, $keys);
    }

    public function jsonSerialize(): array
    {
        return array_map(static function ($value) {
            if ($value instanceof \JsonSerializable) return $value->jsonSerialize();
            if ($value instanceof Jsonable) return json_decode($value->toJson(), true, 512, JSON_THROW_ON_ERROR);
            if ($value instanceof Arrayable) return $value->toArray();
            return $value;
        }, $this->all());
    }

    public function toDotArray(): array
    {
        return Arr::dot($this->toArray());
    }

    public function toArray(): array
    {
        return array_map(fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value, $this->all());
    }

    public function toJson(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->jsonSerialize(), $options, 512);
    }

    public static function readFrom(...$files): array
    {
        $configurations = [];
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext === 'php') {
                $configurations[] = require $file;
            } elseif ($ext === 'json') {
                $contents = file_get_contents($file) ?: throw InvalidJsonFileException::make($file);
                $configurations[] = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } else {
                throw UnsupportedConfigFileTypeException::make($file);
            }
        }
        return array_replace_recursive(...$configurations);
    }
}
