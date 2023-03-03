<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

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
        ':!*.sum',
    ],

    /**
     * The options of http client.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html
     */
    'http_options' => [
        GuzzleHttp\RequestOptions::VERIFY => false,
        GuzzleHttp\RequestOptions::TIMEOUT => 180,
    ],

    /**
     * The options of retry.
     */
    'retry' => [
        'times' => 3,
        'sleep_milliseconds' => 500,
        'when' => static function (Exception $exception): bool {
            return $exception instanceof Illuminate\Http\Client\ConnectionException
                   || $exception instanceof App\Exceptions\TaskException;
        },
    ],

    /**
     * The list of marks.
     */
    'marks' => [
        'diff' => '<diff>',
        'num' => '<num>',
    ],

    /**
     * Force edit mode.
     */
    'edit' => true,

    /**
     * The number of generated messages.
     */
    'num' => 3,

    /**
     * The prompt name.
     */
    'prompt' => 'conventional',

    /**
     * The generator name.
     */
    'generator' => 'openaichat',

    /**
     * The list of generators.
     */
    'generators' => [
        'openai' => [
            'driver' => 'openai',
            'http_options' => [
                // GuzzleHttp\RequestOptions::PROXY => 'http://localhost:8125/v1',
            ],
            'api_key' => env('OPENAI_API_KEY'),
            'completion_parameters' => [
                'model' => 'text-davinci-003', // ['text-davinci-003', 'text-davinci-002']
                // 'prompt' => $prompt,
                'suffix' => null,
                'max_tokens' => 500,
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
                'user' => Illuminate\Support\Str::uuid()->toString(),
            ],
        ],
        'openaichat' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'completion_parameters' => [
                'model' => 'gpt-3.5-turbo', // ['gpt-3.5-turbo', 'gpt-3.5-turbo-0301']
                // 'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.0,
                'top_p' => 1.0,
                'n' => 1,
                'stream' => true,
                'stop' => null,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
                // 'logit_bias' => null,
                'user' => Illuminate\Support\Str::uuid()->toString(),
            ],
        ],
    ],

    /**
     * The list of prompts.
     *
     * @see https://www.conventionalcommits.org
     * @see https://github.com/ahmetkca/CommitAI
     * @see https://github.com/shanginn/git-aicommit
     */
    'prompts' => [
        'conventional' => <<<'conventional'
Here is the output of the `git diff`:
<diff>

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
Write <num> commit messages that accurately summarizes the changes made in the given `git diff` output, following the best practices listed above and the conventional commit format.
Please provide a response in the form of a valid JSON object and do not include "Output:", "Response:" or anything similar to those two before it, in the following format:
[
    {
        "id": 1,
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
conventional
        ,
    ],
];
