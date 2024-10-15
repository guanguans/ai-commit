# ai-commit

<p align="center"><img src="resources/docs/logo.png" alt="logo" style="width: 62%; height: 62%;"></p>

[简体中文](README-zh_CN.md) | [ENGLISH](README.md) | [日本語](README-ja_JP.md)

> Automagically generate conventional git commit message with AI. - 使用 AI 自动生成约定式 git 提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](https://packagist.org/packages/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](https://packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](https://packagist.org/packages/guanguans/ai-commit)

## 支持

- [x] [Bito Cli](https://github.com/gitbito/CLI)
- [x] [ERNIE-Bot-turbo](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot-turbo)
- [x] [ERNIE-Bot](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot)
- [x] [GitHub Copilot CLI](https://github.com/github/gh-copilot)
- [x] [Moonshot](https://platform.moonshot.cn/docs/api-reference)
- [x] [OpenAI Chat](https://platform.openai.com/docs/api-reference/chat)
- [x] [OpenAI](https://platform.openai.com/docs/api-reference/completions)
- [ ] ...

## 环境要求

* PHP >= 7.3

## 安装

### 直接下载 [ai-commit](./builds/ai-commit) 文件

```shell
curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit -#
chmod +x ai-commit
```

### 通过 Composer 安装

```shell
composer global require guanguans/ai-commit --dev -v # 全局
composer require guanguans/ai-commit --dev -v # 本地
```

## 使用

### 快速开始

```shell
./ai-commit config set generators.bito_cli.path bito-cli-path... --global # 配置 Bito Cli 路径(可选)
./ai-commit config set generators.openai.api_key sk-... --global # 配置 OpenAI API key
./ai-commit config set generators.openai_chat.api_key sk-... --global # 配置 OpenAI API key

./ai-commit config set generator openai_chat --global # 配置默认生成器(可选)
./ai-commit commit # 生成且提交信息
```

```shell
╰─ ./ai-commit commit --generator=openai_chat --no-edit --no-verify --ansi                                           ─╯
1. Generating commit message: generating...
{
    "subject": "chore(ai-commit): update settings and commands",
    "body": "- Set Width to 1200\n- Set Height to 742\n- Set TypingSpeed to 10ms\n- Set PlaybackSpeed to 0.2\n- Update git commands and sleep times"
}
1. Generating commit message: ✔

2. Confirming commit message: confirming...
+------------------------------------------------+---------------------------------------+
| subject                                        | body                                  |
+------------------------------------------------+---------------------------------------+
| chore(ai-commit): update settings and commands | - Set Width to 1200                   |
|                                                | - Set Height to 742                   |
|                                                | - Set TypingSpeed to 10ms             |
|                                                | - Set PlaybackSpeed to 0.2            |
|                                                | - Update git commands and sleep times |
+------------------------------------------------+---------------------------------------+

 Do you want to commit this message? (yes/no) [yes]:
 > 


2. Confirming commit message: ✔

3. Committing message: committing...

3. Committing message: ✔

                                                                                                                        
 [OK] Successfully generated and committed message.                                                                     
                                                                                                                                                                                                                                                                                        
```

![](resources/docs/ai-commit-vhs.gif)

### 列出命令

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

### 操作配置

```shell
./ai-commit config [set, get, unset, reset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
./ai-commit config reset key
./ai-commit config list
./ai-commit config edit
```

### 自我更新

```shell
╰─ ./ai-commit self-update                                        ─╯

Checking for a new version...
=============================

                                                                     
 [OK] Updated from version 1.2.4 to v1.2.5.                          
                                                                     
```

### 命令帮助

```shell
╰─ ./ai-commit commit --help                                                                                                               ─╯
Description:
  Automagically generate conventional commit message with AI.

Usage:
  commit [options] [--] [<path>]

Arguments:
  path                                   The working directory [default: "/Users/yaozm/Documents/develop/ai-commit"]

Options:
      --commit-options[=COMMIT-OPTIONS]  Append options for the `git commit` command [default: ["--edit"]] (multiple values allowed)
      --diff-options[=DIFF-OPTIONS]      Append options for the `git diff` command [default: [":!*.lock",":!*.sum"]] (multiple values allowed)
  -g, --generator=GENERATOR              Specify generator name [default: "openai_chat"]
  -p, --prompt=PROMPT                    Specify prompt name of message generated [default: "conventional"]
      --no-edit                          Enable or disable git commit `--no-edit` option
      --no-verify                        Enable or disable git commit `--no-verify` option
  -c, --config[=CONFIG]                  Specify config file
      --retry-times=RETRY-TIMES          Specify times of retry [default: 3]
      --retry-sleep=RETRY-SLEEP          Specify sleep milliseconds of retry [default: 200]
  -h, --help                             Display help for the given command. When no command is given display help for the list command
  -q, --quiet                            Do not output any message
  -V, --version                          Display this application version
      --ansi|--no-ansi                   Force (or disable --no-ansi) ANSI output
  -n, --no-interaction                   Do not ask any interactive question
      --env[=ENV]                        The environment the command should run under
  -v|vv|vvv, --verbose                   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

## 测试

```shell
composer test
```

## 变更日志

请参阅 [CHANGELOG](CHANGELOG.md) 获取最近有关更改的更多信息。

## 贡献指南

请参阅 [CONTRIBUTING](.github/CONTRIBUTING.md) 有关详细信息。

## 安全漏洞

请查看[我们的安全政策](../../security/policy)了解如何报告安全漏洞。

## 贡献者

* [guanguans](https://github.com/guanguans)
* [所有贡献者](../../contributors)

## 协议

MIT 许可证（MIT）。有关更多信息，请参见[协议文件](LICENSE)。
