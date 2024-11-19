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
    // Append options for the `git commit` command.
    'commit_options' => [
        '--edit',
    ],

    // Append options for the `git diff` command.
    'diff_options' => [
        ':!*-lock.json',
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
        GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 30,
        GuzzleHttp\RequestOptions::TIMEOUT => 120,
    ],

    // The options of retry.
    'retry' => [
        'times' => 3,
        'sleep' => 200,
        'when' => static function (Exception $exception): bool {
            return $exception instanceof App\Exceptions\RuntimeException;
        },
    ],

    // The mark of diff.
    'diff_mark' => '<diff>',

    // The mark of type.
    'type_mark' => '<type>',

    // The mark of type prompt.
    'type_prompt_mark' => '<type-prompt>',

    // The type prompt.
    'type_prompt' => '- Use commit type `%s`.',

    'types' => [
        'auto' => 'Automatically generate commit type',
        'feat' => 'A new feature',
        'fix' => 'A bug fix',
        'docs' => 'Documentation only changes',
        'style' => 'Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)',
        'refactor' => 'A code change that neither fixes a bug nor adds a feature',
        'perf' => 'A code change that improves performance',
        'test' => 'Adding missing tests or correcting existing tests',
        'build' => 'Changes that affect the build system or external dependencies (example scopes: gulp, broccoli, npm)',
        'ci' => 'Changes to our CI configuration files and scripts (example scopes: Travis, Circle, BrowserStack, SauceLabs)',
        'chore' => 'Other changes that don\'t modify src or test files',
        'revert' => 'Reverts a previous commit',
    ],

    // Enable or disable git commit `--no-edit` option.
    'no_edit' => false,

    // Enable or disable git commit `--no-verify` option.
    'no_verify' => false,

    // The prompt name.
    'prompt' => 'conventional',

    // The generator name.
    'generator' => 'openai_chat',

    // The list of generators.
    'generators' => [
        'bito_cli' => [
            'driver' => 'bito_cli',
            'binary' => env('BITO_CLI_BINARY', 'bito'),
            'prompt_filename' => 'bito.prompt',
            'options' => [
                '--context' => null,
                '--edit' => null,
                '--file' => null,
                '--help' => null,
                '--ignore' => null,
                '--key' => null,
                '--list' => null,
                '--model' => null,
                '--prompt' => null,
                '--temperature' => null,
                '--version' => null,
            ],
            'parameters' => [
                'cwd' => null,
                'env' => null,
                'input' => null,
                'timeout' => 120,
            ],
        ],
        'ernie_bot' => [
            'driver' => 'ernie_bot',
            'http_options' => [],
            'api_key' => env('ERNIE_API_KEY', '...'),
            'secret_key' => env('ERNIE_SECRET_KEY', '...'),
            'parameters' => [
                // 'messages' => 'required|array',
                'temperature' => 0.95,
                'top_p' => 0.8,
                'penalty_score' => 1.0,
                'stream' => true,
            ],
        ],
        'ernie_bot_turbo' => [
            'driver' => 'ernie_bot_turbo',
            'http_options' => [],
            'api_key' => env('ERNIE_API_KEY', '...'),
            'secret_key' => env('ERNIE_SECRET_KEY', '...'),
            'parameters' => [
                // 'messages' => 'required|array',
                'temperature' => 0.95,
                'top_p' => 0.8,
                'penalty_score' => 1.0,
                'stream' => true,
            ],
        ],
        'github_copilot_cli' => [
            'driver' => 'github_copilot_cli',
            'binary' => env('GITHUB_COPILOT_CLI_BINARY', 'gh'),
            'options' => [
                '--hostname' => null,
            ],
            'parameters' => [
                'cwd' => null,
                'env' => null,
                'input' => null,
                'timeout' => 120,
            ],
        ],
        'github_models_cli' => [
            'driver' => 'github_models_cli',
            'binary' => env('GITHUB_MODELS_CLI_BINARY', 'gh'),
            'model' => env('GITHUB_MODELS_CLI_MODEL', 'gpt-4o-mini'),
            'options' => [
                '--max-tokens' => null,
                '--system-prompt' => 'You are a git commit message generator.',
                '--temperature' => null,
                '--top-p' => null,
            ],
            'parameters' => [
                'cwd' => null,
                'env' => null,
                'input' => null,
                'timeout' => 120,
            ],
        ],
        'moonshot' => [
            'driver' => 'moonshot',
            'http_options' => [
                // GuzzleHttp\RequestOptions::PROXY => 'https://proxy.com/v1',
            ],
            'api_key' => env('MOONSHOT_API_KEY', 'sk-...'),
            'parameters' => [
                'model' => 'moonshot-v1-8k', // moonshot-v1-8k,moonshot-v1-32k,moonshot-v1-128k
                // 'messages' => 'required|array',
                'max_tokens' => 600,
                'temperature' => 0.0,
                'top_p' => 1.0,
                'n' => 1,
                'stream' => true,
                'stop' => null,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
        ],
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY', 'sk-...'),
            'parameters' => [
                'model' => 'text-davinci-003', // text-davinci-003,text-davinci-002,text-curie-001,text-babbage-001,text-ada-001
                // 'prompt' => 'string|array',
                'suffix' => null,
                'max_tokens' => 600,
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
            ],
        ],
        'openai_chat' => [
            'driver' => 'openai_chat',
            'http_options' => [
                // GuzzleHttp\RequestOptions::PROXY => 'https://proxy.com/v1',
            ],
            'api_key' => env('OPENAI_API_KEY', 'sk-...'),
            'parameters' => [
                'model' => 'gpt-3.5-turbo', // gpt-4,gpt-4-0613,gpt-4-32k,gpt-4-32k-0613,gpt-3.5-turbo,gpt-3.5-turbo-0613,gpt-3.5-turbo-16k,gpt-3.5-turbo-16k-0613
                // 'messages' => 'required|array',
                'max_tokens' => 600,
                'temperature' => 0.0,
                'top_p' => 1.0,
                'n' => 1,
                'stream' => true,
                'stop' => null,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
                // 'logit_bias' => null,
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
            <type-prompt>
            Write 1 commit messages that accurately summarizes the changes made in the given `git diff` output, following the best practices listed above and the conventional commit format.
            Please provide a response in the form of a valid JSON object and do not include "Output:", "Response:" or anything similar to those two before it, in the following format:
            {
                "subject": "<type>(<scope>): <subject>",
                "body": "<BODY (bullet points)>"
            }

            Here is the output of the `git diff`:
            <diff>
            conventional
        ,
    ],
];
