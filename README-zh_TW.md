# ai-commit

[//]: # (https://looka.com)

[//]: # (<p align="center"><img src="resources/docs/logo.png" alt="logo" style="width: 62%; height: 62%;"></p>)
<p align="center"><img src="resources/docs/ai-commit-vhs.gif" alt="ai-commit-vhs"></p>

[ENGLISH](README.md) | [日本語](README-ja.md) | [简体中文](README-zh_CN.md) | [繁體中文](README-zh_TW.md)

> Automagically generate conventional git commit message with AI. - 使用 AI 自动生成约定式 git 提交信息。

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/guanguans/ai-commit)
[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](https://packagist.org/packages/guanguans/ai-commit)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)](https://github.com/guanguans/ai-commit/releases/latest)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](https://packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](https://packagist.org/packages/guanguans/ai-commit)

## 支援

- [x] [Bito Cli](https://github.com/gitbito/CLI)
- [x] [ERNIE-Bot-turbo](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot-turbo)
- [x] [ERNIE-Bot](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot)
- [x] [GitHub Copilot CLI](https://github.com/github/gh-copilot)
- [x] [GitHub Models CLI](https://github.com/github/gh-models)
- [x] [Moonshot](https://platform.moonshot.cn/docs/api-reference)
- [x] [OpenAI Chat](https://platform.openai.com/docs/api-reference/chat)
- [x] [OpenAI](https://platform.openai.com/docs/api-reference/completions)
- [ ] ...

## 系統需求

* PHP >= 8.2

## 安裝

### 下載 [ai-commit](./builds/ai-commit) 檔案

```shell
curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit -#
chmod +x ai-commit
```

### 透過 Composer 安裝

```shell
composer global require guanguans/ai-commit --dev -v # 全域安裝
composer require guanguans/ai-commit --dev -v # 本地安裝
```

## 使用方法

### 快速開始

<details>
<summary>details</summary>

```shell
./ai-commit config set generators.bito_cli.binary bito-cli-binary... --global # Config Bito CLI binary(Optional)
./ai-commit config set generators.ernie_bot.api_key api-key... --global # Config Ernie API key
./ai-commit config set generators.ernie_bot_turbo.api_key api-key... --global # Config Ernie API key
./ai-commit config set generators.github_copilot_cli.binary gh-cli-binary... --global # Config Github CLI binary(Optional)
./ai-commit config set generators.github_models_cli.binary gh-cli-binary... --global # Config Github CLI binary(Optional)
./ai-commit config set generators.moonshot.api_key sk-... --global # Config Moonshot API key
./ai-commit config set generators.openai.api_key sk-... --global # Config OpenAI API key
./ai-commit config set generators.openai_chat.api_key sk-... --global # Config OpenAI API key

./ai-commit config set generator openai_chat --global # Config default generator(Optional)
./ai-commit commit # Generate and commit message using the default generator
./ai-commit commit --generator=github_copilot_cli # Generate and commit message using the specified generator
```

```shell
╰─ ./ai-commit commit --generator=bito_cli --no-edit --no-verify --ansi                                                                                                      ─╯
1. Generating commit message: generating...

 Please choose commit type [Automatically generate commit type]:
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

</details>

### 指令列表

<details>
<summary>details</summary>

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

</details>

### 設定操作

<details>
<summary>details</summary>

```shell
./ai-commit config [set, get, unset, reset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
./ai-commit config reset key
./ai-commit config list
./ai-commit config edit
```

</details>

### 自動更新

<details>
<summary>details</summary>

```shell
╰─ ./ai-commit self-update                                        ─╯

Checking for a new version...
=============================

                                                                     
 [OK] Updated from version 1.2.4 to v1.2.5.                          
                                                                     
```

</details>

### 指令說明

<details>
<summary>details</summary>

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
  -v|vv|vvv, --verbose                   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

</details>

## 測試

```shell
composer test
```

## 更新日誌

有關最近的變更，請參閱 [CHANGELOG](CHANGELOG.md)。

## 貢獻

詳情請參閱 [CONTRIBUTING](.github/CONTRIBUTING.md)。

## 安全漏洞

請查看[我們的安全政策](../../security/policy)了解如何回報安全漏洞。

## 致謝

* [guanguans](https://github.com/guanguans)
* [所有貢獻者](../../contributors)

## 授權條款

MIT 授權條款 (MIT)。更多資訊請參閱 [授權檔案](LICENSE)。
