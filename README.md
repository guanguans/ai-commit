<br>
<div align="center">
  <a href="https://github.com/guanguans/ai-commit/actions">
    <img src="https://github.com/guanguans/ai-commit/workflows/tests/badge.svg" alt="tests">
  </a>
  <a href="https://github.com/guanguans/ai-commit/actions">
    <img src="https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg" alt="check & fix styling">
  </a>
  <a href="https://codecov.io/gh/guanguans/ai-commit">
    <img src="https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4" alt="codecov">
  </a>
  <a href="https://packagist.org/packages/guanguans/ai-commit">
    <img src="https://poser.pugx.org/guanguans/ai-commit/v" alt="Latest Stable Version">
  </a>
  <a href="https://github.com/guanguans/ai-commit/releases/latest">
    <img src="https://img.shields.io/github/v/release/guanguans/ai-commit" alt="GitHub release (latest by date)">
  </a>
  <br>
  <a href="https://packagist.org/packages/guanguans/ai-commit">
    <img src="https://poser.pugx.org/guanguans/ai-commit/downloads" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/guanguans/ai-commit">
    <img src="https://poser.pugx.org/guanguans/ai-commit/license" alt="License">
  </a>
</div>
<br>
<br>

## Table of Contents

- [ai-commit](#ai-commit)
- [Support Docs](#support-docs)
- [Requirements](#requirements)
- [Installation](#installation)
    - [Download the ai-commit file](#download-the-ai-commit-file)
    - [Install via Composer](#install-via-composer)
- [Usage](#usage)
    - [Quick start](#quick-start)
    - [List commands](#list-commands)
    - [Operate config](#operate-config)
    - [Self update](#self-update)
    - [Command help](#command-help)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

  <br> 
  
# ai-commit
> Automagically generate conventional git commit message with AI. - 使用 AI 自动生成约定式 git 提交信息。

[//]: # (https://looka.com)
[//]: # (<p align="center"><img src="resources/docs/logo.png" alt="logo" style="width: 62%; height: 62%;"></p>)
<p align="center"><img src="resources/docs/ai-commit-vhs.gif" alt="ai-commit-vhs"></p>

[简体中文](README-zh_CN.md) | [ENGLISH](README.md) | [日本語](README-ja.md)

<br>


# Support Docs

- [x] [Bito Cli](https://github.com/gitbito/CLI)
- [x] [ERNIE-Bot-turbo](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot-turbo)
- [x] [ERNIE-Bot](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot)
- [x] [GitHub Copilot CLI](https://github.com/github/gh-copilot)
- [x] [Moonshot](https://platform.moonshot.cn/docs/api-reference)
- [x] [OpenAI Chat](https://platform.openai.com/docs/api-reference/chat)
- [x] [OpenAI](https://platform.openai.com/docs/api-reference/completions)

<br>

# Requirements

+ PHP >= 7.3

<br>

# Installation

+ Download the [ai-commit](./builds/ai-commit) file

+ ```shell
  curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit -#
  chmod +x ai-commit
  ```

+ Install via Composer

+ ```shell
  composer global require guanguans/ai-commit --dev -v # global
  composer require guanguans/ai-commit --dev -v # local
  ```
<br>

# Usage

+ Quick start

+ ```shell
  ./ai-commit config set generators.bito_cli.binary bito-cli-binary... --global # Config Bito cli binary(Optional)
  ./ai-commit config set generators.ernie_bot.api_key api-key... --global # Config Ernie API key
  ./ai-commit config set generators.ernie_bot_turbo.api_key api-key... --global # Config Ernie API key
  ./ai-commit config set generators.github_copilot_cli.binary gh-cli-binary... --global # Config Github cli binary(Optional)
  ./ai-commit config set generators.moonshot.api_key sk-... --global # Config Moonshot API key
  ./ai-commit config set generators.openai.api_key sk-... --global # Config OpenAI API key
  ./ai-commit config set generators.openai_chat.api_key sk-... --global # Config OpenAI API key

  ./ai-commit config set generator openai_chat --global # Config default generator(Optional)
  ./ai-commit commit # Generate and commit message using the default generator
  ./ai-commit commit --generator=github_copilot_cli # Generate and commit message using the specified generator
  ```

+ ```shell
  ╰─ ./ai-commit commit --generator=bito_cli --no-edit --no-verify --ansi                                                                                                      ─╯
  1. Generating commit message: generating...

   Please choice commit type [Automatically generate commit type]:
    [auto    ] Automatically generate commit type
    [feat    ] A new feature
    [fix     ] A bug fix
    [docs    ] Documentation only changes
    [style   ] Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
    [refactor] A code change that neither fixes a bug nor adds a feature
    [perf    ] A code change that improves performance
    [test    ] Adding missing tests or correcting existing tests
    [build   ] Changes that affect the build system or external dependencies (example scopes: gulp, broccoli, npm)
    [ci      ] Changes to our CI configuration files and scripts (example scopes: Travis, Circle, BrowserStack, SauceLabs)
    [chore   ] Other changes that don't modify src or test files
    [revert  ] Reverts a previous commit
   > chore

    RUN  'bito'
    ERR  Model in use: BASIC
    ERR  
    ERR  
    OUT  {
    OUT      "subject": "chore(ai-commit): update tape and gif resources",
    OUT      "body": "- Adjusted width and height settings in ai-commit.tape\n- Changed commit command generator from openai_chat to bito_cli\n- Updated ai-commit-vhs.gif file with new binary data"
    OUT  }
    OUT  
    OUT  
    RES  Command ran successfully
  1. Generating commit message: ✔

  2. Confirming commit message: confirming...
  +-------------------------------------------------+-----------------------------------------------------------------+
  | subject                                         | body                                                            |
  +-------------------------------------------------+-----------------------------------------------------------------+
  | chore(ai-commit): update tape and gif resources | - Adjusted width and height settings in ai-commit.tape          |
  |                                                 | - Changed commit command generator from openai_chat to bito_cli |
  |                                                 | - Updated ai-commit-vhs.gif file with new binary data           |
  +-------------------------------------------------+-----------------------------------------------------------------+

   Do you want to commit this message? (yes/no) [yes]:
   > 


  2. Confirming commit message: ✔

  3. Committing message: committing...

  3. Committing message: ✔

                                                                                                                        
   [OK] Successfully generated and committed message.                                                                     
                                                                                                                                                                                                                                                                                                                                                                                                      
  ```

![](resources/docs/ai-commit-vhs.gif)

+ List commands

  ```shell
  ╰─ ./ai-commit list                                                     ─╯
  
    
            _____    _____                          _ _   
      /\   |_   _|  / ____|                        (_) |  
     /  \    | |   | |     ___  _ __ ___  _ __ ___  _| |_ 
    / /\ \   | |   | |    / _ \| '_ ` _ \| '_ ` _ \| | __|
   / ____ \ _| |_  | |___| (_) | | | | | | | | | | | | |_ 
  /_/    \_\_____|  \_____\___/|_| |_| |_|_| |_| |_|_|\__|
                                                        
                                                        

    1.2.5

    USAGE: ai-commit <command> [options] [arguments]

    commit      Automagically generate conventional commit message with AI.
    completion  Dump the shell completion script
    config      Manage config options.
    self-update Allows to self-update a build application
    thanks      Thanks for using this tool.
  ```

+ Operate config

  ```shell
  ./ai-commit config [set, get, unset, reset, list, edit] key value --global

  ./ai-commit config set key value
  ./ai-commit config get key
  ./ai-commit config unset key
  ./ai-commit config reset key
  ./ai-commit config list
  ./ai-commit config edit
  ```

+ Self update

  ```shell
  ╰─ ./ai-commit self-update                                        ─╯

  Checking for a new version...
  =============================

                                                                     
   [OK] Updated from version 1.2.4 to v1.2.5.                          
                                                                     
  ```

+ Command help

  ```shell
  ╰─ ./ai-commit commit --help                                                                                                                               ─╯
  Description:
    Automagically generate conventional commit message with AI.

  Usage:
    commit [options] [--] [<path>]

  Arguments:
    path                                   The working directory [default: "/Users/yaozm/Documents/develop/ai-commit"]

  Options:
        --commit-options[=COMMIT-OPTIONS]  Append options for the `git commit` command [default: ["--edit"]] (multiple values allowed)
        --diff-options[=DIFF-OPTIONS]      Append options for the `git diff` command [default: [":!*-lock.json",":!*.lock",":!*.sum"]] (multiple values allowed)
    -g, --generator=GENERATOR              Specify generator name [default: "openai_chat"]
    -p, --prompt=PROMPT                    Specify prompt name of message generated [default: "conventional"]
        --no-edit                          Enable or disable git commit `--no-edit` option
        --no-verify                        Enable or disable git commit `--no-verify` option
    -c, --config[=CONFIG]                  Specify config file
        --retry-times=RETRY-TIMES          Specify times of retry [default: 3]
        --retry-sleep=RETRY-SLEEP          Specify sleep milliseconds of retry [default: 200]
        --dry-run                          Only generate message without commit
        --diff[=DIFF]                      Specify diff content
    -h, --help                             Display help for the given command. When no command is given display help for the list command
    -q, --quiet                            Do not output any message
    -V, --version                          Display this application version
        --ansi|--no-ansi                   Force (or disable --no-ansi) ANSI output
    -n, --no-interaction                   Do not ask any interactive question
        --env[=ENV]                        The environment the command should run under
    -v|vv|vvv, --verbose                   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for 
  debug
  ```
<br>

# Testing

```shell
composer test
```
<br>

# Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

<br>

# Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

<br>

# Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

<br>

# Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

<br>

# License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
