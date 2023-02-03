<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Illuminate\Support\Str;

return [
    /**
     * Append options for the `git commit` command.
     */
    'commit_options' => [
        '--edit',
    ],

    /**
     * Append options for the `git diff` command.
     */
    'diff_options' => [
        ':!*.lock',
    ],

    /**
     * The prompt name.
     */
    'prompt' => 'en',

    /**
     * The number of generated messages.
     */
    'num' => 3,

    /**
     * The generator name.
     */
    'generator' => 'openai',

    /**
     * The mark of diff.
     */
    'diff_mark' => '<diff>',

    /**
     * The mark of number.
     */
    'num_mark' => '<num>',

    /**
     * The list of generators.
     */
    'generators' => [
        'openai' => [
            'driver' => 'openai',
            'http_options' => [
                'connect_timeout' => 3,
                'timeout' => 60,
            ],
            'api_key' => 'sk-...',
            'completion_parameters' => [
                'model' => 'text-davinci-003',
                // 'prompt' => $prompt,
                'suffix' => null,
                'max_tokens' => 3000,
                'temperature' => 0.0,
                'top_p' => 1.0,
                'n' => 1,
                'stream' => true,
                'logprobs' => null,
                'echo' => false,
                'stop' => null,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
                'best_of' => 1,
                // 'logit_bias' => null,
                'user' => Str::uuid()->toString(),
            ],
        ],
    ],

    /**
     * The list of prompts.
     *
     * @see https://github.com/ahmetkca/CommitAI
     * @see https://github.com/shanginn/git-aicommit
     */
    'prompts' => [
        'en' => <<<'prompt'
Here is the output of the `git diff`:
```
<diff>
```
Here are some best practices for writing commit messages:
- Write clear, concise, and descriptive messages that explain the changes made in the commit.
- Use the present tense and active voice in the message, for example, "Fix bug" instead of "Fixed bug."
- Use the imperative mood, which gives the message a sense of command, e.g. "Add feature" instead of "Added feature"
- Limit the subject line to 72 characters or less.
- Capitalize the subject line.
- Do not end the subject line with a period.
- Limit the body of the message to 256 characters or less.
- Use a blank line between the subject and the body of the message.
- Use the body of the message to provide additional context or explain the reasoning behind the changes.
- Avoid using general terms like "update" or "change" in the subject line, be specific about what was updated or changed.
- Explain, What was done at a glance in the subject line, and provide additional context in the body of the message.
- Why the change was necessary in the body of the message.
- The details about what was done in the body of the message.
- Any useful details concerning the change in the body of the message.
- Use a hyphen (-) for the bullet points in the body of the message.
Write <num> commit messages that accurately summarizes the changes made in the given `git diff` output, following the best practices listed above.
Please provide a response in the form of a valid JSON object and do not include "Output:", "Response:" or anything similar to those two before it, in the following format:
[
    {
        "id": 1,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    },
    {
        "id": 2,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    },
    ...
    {
        "id": n,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    }
]
prompt,
        'cn' => <<<'prompt'
这是 `git diff` 的输出：
```
<diff>
```
以下是编写提交消息的一些最佳实践：
- 编写清晰、简明和描述性的消息来解释提交中所做的更改。
- 在消息中使用现在时和主动语态，例如，“修复错误”而不是“修复错误”。
- 使用祈使语气，使信息具有命令感，例如“添加功能”而不是“添加的功能”
- 将主题行限制在 72 个字符以内。
- 将主题行大写。
- 不要以句号结束主题行。
- 将邮件正文限制为 256 个字符或更少。
- 在邮件主题和正文之间使用一个空行。
- 使用邮件正文提供额外的上下文或解释更改背后的原因。
- 避免在主题行中使用“更新”或“更改”等笼统术语，具体说明更新或更改的内容。
- 解释，在主题行中一目了然地做了什么，并在邮件正文中提供额外的上下文。
- 为什么需要在邮件正文中进行更改。
- 有关在消息正文中执行的操作的详细信息。
- 有关消息正文更改的任何有用详细信息。
- 使用连字符 (-) 作为邮件正文中的要点。
按照上面列出的最佳实践，编写 <num> 提交消息，准确总结在给定的 `git diff` 输出中所做的更改。
请以有效的 JSON 对象形式提供响应，并且不要包含“Output:”、“Response:”或与前面两个类似的任何内容，格式如下：
[
    {
        "id": 1,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    },
    {
        "id": 2,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    },
    ...
    {
        "id": n,
        "subject": "<type>(<scope>): <subject>",
        "body": "<BODY (bullet points)>"
    }
]
prompt
    ],
];
