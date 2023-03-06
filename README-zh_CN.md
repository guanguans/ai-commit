# ai-commit

![](docs/ai-commit.gif)

[简体中文](README-zh_CN.md) | [ENGLISH](README.md)

> Automagically generate conventional git commit messages with AI. - 使用 AI 自动生成约定式 git 提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](//packagist.org/packages/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](//packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](//packagist.org/packages/guanguans/ai-commit)

## 环境要求

* PHP >= 7.3

## 安装

### 直接下载 [ai-commit](./builds/ai-commit) 文件

```shell
curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit --progress
chmod +x ai-commit
```

### 通过 Composer 安装

```shell
# 全局
composer global require guanguans/ai-commit --dev -v

# 本地
composer require guanguans/ai-commit --dev -v
```

## 使用

### 快速开始

```shell
# 配置 OpenAI API key(https://platform.openai.com/account/api-keys)
./ai-commit config set generators.openaichat.api_key sk-... --global
./ai-commit config set generators.openai.api_key sk-... --global

# 生成且提交信息
./ai-commit commit
```

```shell
╰─ ./ai-commit commit                                                                                                   ─╯
1. Generating commit messages: generating...

[
    {
        "id": 1,
        "subject": "Docs(README): Update README files",
        "body": "- Update README-zh_CN.md\n- Update README.md\n- Explain how to configure OpenAI API key"
    },
    {
        "id": 2,
        "subject": "Install(Composer): Add install instructions",
        "body": "- Update README.md\n- Add instructions for global and local install via Composer"
    },
    {
        "id": 3,
        "subject": "Commit Messages): Add best practices",
        "body": "- Update README.md\n- Add best practices for writing commit messages"
    }
1. Generating commit messages: ✔
2. Choosing commit message: choosing...
+----+---------------------------------------------+--------------------------------------------------------------+
| id | subject                                     | body                                                         |
+----+---------------------------------------------+--------------------------------------------------------------+
| 1  | Docs(README): Update README files           | - Update README-zh_CN.md                                     |
|    |                                             | - Update README.md                                           |
|    |                                             | - Explain how to configure OpenAI API key                    |
+----+---------------------------------------------+--------------------------------------------------------------+
| 2  | Install(Composer): Add install instructions | - Update README.md                                           |
|    |                                             | - Add instructions for global and local install via Composer |
+----+---------------------------------------------+--------------------------------------------------------------+
| 3  | Commit Messages): Add best practices        | - Update README.md                                           |
|    |                                             | - Add best practices for writing commit messages             |
+----+---------------------------------------------+--------------------------------------------------------------+

 Please choice a commit message [Docs(README): Update README files]:
  [1] Docs(README): Update README files
  [2] Install(Composer): Add install instructions
  [3] Commit Messages): Add best practices
 > Docs(README): Update README files

2. Choosing commit message: ✔
3. Committing message: committing...
[main 8c07fa3] Docs(README): Update README files
 1 file changed, 1 insertion(+), 29 deletions(-)
3. Committing message: ✔

                                                                                                                        
 [OK] Generate and commit messages have succeeded                                                                       
                                                                                                                        

```

![](docs/ai-commit-vhs.gif)

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

  commit      Automagically generate conventional commit messages with AI.
  completion  Dump the shell completion script
  config      Manage config options.
  self-update Allows to self-update a build application
  thanks      Thanks for using this tool.
```

### 操作配置

```shell
./ai-commit config [set, get, unset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
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
╰─ ./ai-commit commit --help                                                                                                                                        ─╯
Description:
  Automagically generate conventional commit messages with AI.

Usage:
  commit [options] [--] [<path>]

Arguments:
  path                                                     The working directory [default: "/Users/yaozm/Documents/develop/ai-commit"]

Options:
      --commit-options[=COMMIT-OPTIONS]                    Append options for the `git commit` command [default: ["--edit"]] (multiple values allowed)
      --diff-options[=DIFF-OPTIONS]                        Append options for the `git diff` command [default: [":!*.lock",":!*.sum"]] (multiple values allowed)
  -g, --generator=GENERATOR                                Specify generator name [default: "openaichat"]
  -p, --prompt=PROMPT                                      Specify prompt name of messages generated [default: "conventional"]
      --num=NUM                                            Specify number of generated messages [default: 3]
      --no-edit                                            Force no edit mode
  -c, --config[=CONFIG]                                    Specify config file
      --retry-times=RETRY-TIMES                            Specify times of retry [default: 3]
      --retry-sleep-milliseconds=RETRY-SLEEP-MILLISECONDS  Specify sleep milliseconds of retry [default: 500]
  -h, --help                                               Display help for the given command. When no command is given display help for the list command
  -q, --quiet                                              Do not output any message
  -V, --version                                            Display this application version
      --ansi|--no-ansi                                     Force (or disable --no-ansi) ANSI output
  -n, --no-interaction                                     Do not ask any interactive question
      --env[=ENV]                                          The environment the command should run under
  -v|vv|vvv, --verbose                                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
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
