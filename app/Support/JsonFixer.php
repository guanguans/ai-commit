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

use App\Exceptions\RuntimeException;

/**
 * This file is modified from https://github.com/adhocore/php-json-fixer.
 */
final class JsonFixer
{
    private array $stack = [];

    /** @var bool If current char is within a string */
    private bool $inStr = false;

    /** @var bool Whether to throw Exception on failure */
    private bool $silent = false;

    /** @var array<string, string> The complementary pairs */
    private array $pairs = [
        '{' => '}',
        '[' => ']',
        '"' => '"',
    ];

    /** @var int The last seen object `{` type position */
    private int $objectPos = -1;

    /** @var int The last seen array `[` type position */
    private int $arrayPos = -1;

    /** @var string Missing value. (Options: true, false, null) */
    private string $missingValue = 'null';

    /**
     * Set/unset silent mode.
     */
    public function silent(bool $silent = true): self
    {
        $this->silent = $silent;

        return $this;
    }

    /**
     * Set missing value.
     */
    public function missingValue(string $value): self
    {
        $this->missingValue = $value;

        return $this;
    }

    /**
     * Fix the truncated JSON.
     *
     * @throws \RuntimeException
     */
    public function fix(string $json): string
    {
        [$head, $json, $tail] = $this->trim($json);

        if (empty($json) || $this->isValid($json)) {
            return $json;
        }

        if (null !== ($tmpJson = $this->quickFix($json))) {
            return $tmpJson;
        }

        $this->reset();

        return $head.$this->doFix($json).$tail;
    }

    // trait PadsJson
    public function pad(string $tmpJson): string
    {
        if (!$this->inStr) {
            $tmpJson = rtrim($tmpJson, ',');

            while (',' === $this->lastToken()) {
                $this->popToken();
            }
        }

        $tmpJson = $this->padLiteral($tmpJson);
        $tmpJson = $this->padObject($tmpJson);

        return $this->padStack($tmpJson);
    }

    private function trim(string $json): array
    {
        preg_match('/^(\s*)(\S+)(\s*)$/', $json, $match);

        $match += ['', '', '', ''];
        $match[2] = trim($json);

        array_shift($match);

        return $match;
    }

    /**
     * @noinspection JsonEncodingApiUsageInspection
     */
    private function isValid(string $json): bool
    {
        /** @psalm-suppress UnusedFunctionCall */
        json_decode($json);

        return \JSON_ERROR_NONE === json_last_error();
    }

    private function quickFix(string $json): ?string
    {
        if (isset($this->pairs[$json]) && 1 === \strlen($json)) {
            return $json.$this->pairs[$json];
        }

        if ('"' !== $json[0]) {
            return $this->maybeLiteral($json);
        }

        return $this->padString($json);
    }

    private function reset(): void
    {
        $this->stack = [];
        $this->inStr = false;
        $this->objectPos = -1;
        $this->arrayPos = -1;
    }

    private function maybeLiteral(string $json): ?string
    {
        if (!\in_array($json[0], ['t', 'f', 'n'], true)) {
            return null;
        }

        foreach (['true', 'false', 'null'] as $literal) {
            if (str_starts_with($literal, $json)) {
                return $literal;
            }
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }

    private function doFix(string $json): string
    {
        [$index, $char] = [-1, ''];

        while (isset($json[++$index])) {
            [$prev, $char] = [$char, $json[$index]];

            if (!\in_array($char, [' ', "\n", "\r"], true)) {
                $this->stack($prev, $char, $index);
            }
        }

        return $this->fixOrFail($json);
    }

    /**
     * @psalm-suppress UnusedParam
     */
    private function stack(string $prev, string $char, int $index): void
    {
        if ($this->maybeStr($prev, $char, $index)) {
            return;
        }

        $last = $this->lastToken();

        if (\in_array($last, [',', ':', '"'], true) && preg_match('/\"|\d|\{|\[|t|f|n/', $char)) {
            $this->popToken();
        }

        if (\in_array($char, [',', ':', '[', '{'], true)) {
            $this->stack[$index] = $char;
        }

        $this->updatePos($char, $index);
    }

    private function lastToken()
    {
        return end($this->stack);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function popToken(?string $token = null)
    {
        // Last one
        if (null === $token) {
            return array_pop($this->stack);
        }

        $keys = array_reverse(array_keys($this->stack));

        foreach ($keys as $key) {
            if ($this->stack[$key] === $token) {
                unset($this->stack[$key]);

                break;
            }
        }
    }

    private function maybeStr(string $prev, string $char, int $index): bool
    {
        if ('\\' !== $prev && '"' === $char) {
            $this->inStr = !$this->inStr;
        }

        if ($this->inStr && '"' !== $this->lastToken()) {
            $this->stack[$index] = '"';
        }

        return $this->inStr;
    }

    private function updatePos(string $char, int $index): void
    {
        if ('{' === $char) {
            $this->objectPos = $index;
        } elseif ('}' === $char) {
            $this->popToken('{');
            $this->objectPos = -1;
        } elseif ('[' === $char) {
            $this->arrayPos = $index;
        } elseif (']' === $char) {
            $this->popToken('[');
            $this->arrayPos = -1;
        }
    }

    private function fixOrFail(string $json): string
    {
        $length = \strlen($json);
        $tmpJson = $this->pad($json);

        if ($this->isValid($tmpJson)) {
            return $tmpJson;
        }

        if ($this->silent) {
            return $json;
        }

        throw new RuntimeException(\sprintf('Could not fix JSON (tried padding `%s`)', substr($tmpJson, $length)));
    }

    private function padLiteral(string $tmpJson): string
    {
        if ($this->inStr) {
            return $tmpJson;
        }

        $match = preg_match('/(tr?u?e?|fa?l?s?e?|nu?l?l?)$/', $tmpJson, $matches);

        if (!$match || null === ($literal = $this->maybeLiteral($matches[1]))) {
            return $tmpJson;
        }

        return substr($tmpJson, 0, -\strlen($matches[1])).$literal;
    }

    private function padStack(string $tmpJson): string
    {
        foreach (array_reverse($this->stack, true) as $token) {
            if (isset($this->pairs[$token])) {
                $tmpJson .= $this->pairs[$token];
            }
        }

        return $tmpJson;
    }

    private function padObject(string $tmpJson): string
    {
        if (!$this->objectNeedsPadding($tmpJson)) {
            return $tmpJson;
        }

        $part = substr($tmpJson, $this->objectPos + 1);

        if (preg_match('/(\s*\"[^"]+\"\s*:\s*[^,]+,?)+$/', $part)) {
            return $tmpJson;
        }

        if ($this->inStr) {
            $tmpJson .= '"';
        }

        $tmpJson = $this->padIf($tmpJson, ':');
        $tmpJson .= $this->missingValue;

        if ('"' === $this->lastToken()) {
            $this->popToken();
        }

        return $tmpJson;
    }

    private function objectNeedsPadding(string $tmpJson): bool
    {
        $last = substr($tmpJson, -1);
        $empty = '{' === $last && !$this->inStr;

        return !$empty && $this->arrayPos < $this->objectPos;
    }

    private function padString(string $string): ?string
    {
        $last = substr($string, -1);
        $last2 = substr($string, -2);

        if ('\"' === $last2 || '"' !== $last) {
            return $string.'"';
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }

    private function padIf(string $string, string $substr): string
    {
        if (substr($string, -\strlen($substr)) !== $substr) {
            return $string.$substr;
        }

        return $string;
    }
}
