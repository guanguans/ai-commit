<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection PhpUnused */
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

dataset('invalid jsons', [
    [
        'json' => '',
        'expect' => '',
    ],
    [
        'json' => '"',
        'expect' => '""',
    ],
    [
        'json' => '"a"',
        'expect' => '"a"',
    ],
    [
        'json' => 'true',
        'expect' => 'true',
    ],
    [
        'json' => 'false',
        'expect' => 'false',
    ],
    [
        'json' => 'null',
        'expect' => 'null',
    ],
    [
        'json' => 'fal',
        'expect' => 'false',
    ],
    [
        'json' => 't',
        'expect' => 'true',
    ],
    [
        'json' => 'nu',
        'expect' => 'null',
    ],
    [
        'json' => '{',
        'expect' => '{}',
    ],
    [
        'json' => '[',
        'expect' => '[]',
    ],
    [
        'json' => '12.34',
        'expect' => '12.34',
    ],
    [
        'json' => '"str',
        'expect' => '"str"',
    ],
    [
        'json' => '[{',
        'expect' => '[{}]',
    ],
    [
        'json' => '[1',
        'expect' => '[1]',
    ],
    [
        'json' => '["',
        'expect' => '[""]',
    ],
    [
        'json' => '[1,',
        'expect' => '[1]',
    ],
    [
        'json' => '[1,{',
        'expect' => '[1,{}]',
    ],
    [
        'json' => '["a',
        'expect' => '["a"]',
    ],
    [
        'json' => '["b,',
        'expect' => '["b,"]',
    ],
    [
        'json' => '["b",{"',
        'expect' => '["b",{"":null}]',
    ],
    [
        'json' => '["b",{"a',
        'expect' => '["b",{"a":null}]',
    ],
    [
        'json' => '["b",{"a":',
        'expect' => '["b",{"a":null}]',
    ],
    [
        'json' => '["b",{"a":[t',
        'expect' => '["b",{"a":[true]}]',
    ],
    [
        'json' => '{"a":2',
        'expect' => '{"a":2}',
    ],
    [
        'json' => '{"a":',
        'expect' => '{"a":null}',
    ],
    [
        'json' => '{"a"',
        'expect' => '{"a":null}',
    ],
    [
        'json' => '{"',
        'expect' => '{"":null}',
    ],
    [
        'json' => '{"a":1.2,',
        'expect' => '{"a":1.2}',
    ],
    [
        'json' => '{"a":"',
        'expect' => '{"a":""}',
    ],
    [
        'json' => '{"a":[',
        'expect' => '{"a":[]}',
    ],
    [
        'json' => '{"a":"b","b":["',
        'expect' => '{"a":"b","b":[""]}',
    ],
    [
        'json' => '{"a":"b","b":[t',
        'expect' => '{"a":"b","b":[true]}',
    ],
    [
        'json' => '[ {"id":1, "data": []}, {"id":2, "data": [',
        'expect' => '[ {"id":1, "data": []}, {"id":2, "data": []}]',
    ],
]);
