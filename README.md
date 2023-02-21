# ai-commit

![](docs/ai-commit.gif)

[简体中文](README-zh_CN.md) | [ENGLISH](README.md)

> Automagically generate conventional git commit messages with AI. - 使用 AI 自动生成约定式 git 提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](//packagist.org/packages/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](//packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](//packagist.org/packages/guanguans/ai-commit)
![GitHub repo size](https://img.shields.io/github/repo-size/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)

## Features

* By default, commit messages is generated in conventional format
* You can customize the AI driver that generates commit messages(currently only supported by Open AI)
* You can customize the prompt template that generates commit messages
* You can customize the number of generates commit messages to be selected

## Requirement

* PHP >= 7.3

## Installation

### Download the [ai-commit](./builds/ai-commit) file

```shell
curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit --progress
chmod +x ai-commit
```

### Install via Composer

```shell
# global
composer global require guanguans/ai-commit --dev -v

# local
composer require guanguans/ai-commit --dev -v
```

## Usage

### Quick Start

```shell
# Config OpenAI API key(https://platform.openai.com/account/api-keys)
./ai-commit config set generators.openai.api_key sk-... --global

# Generate and commit message
./ai-commit commit
```

```shell
1. Checking run environment: ✔
2. Generating commit messages: generating...

[
    {
        "id": 1,
        "subject": "Docs(README): Configure OpenAI API key",
        "body": "- Update README-zh_CN.md\n- Update README.md\n- Explain how to configure OpenAI API key"
    },
    {
        "id": 2,
        "subject": "Install(Composer): Add global and local install instructions",
        "body": "- Update README.md\n- Add instructions for global and local install via Composer"
    },
    {
        "id": 3,
        "subject": "Usage(Commit Messages): Add best practices",
        "body": "- Update README.md\n- Add best practices for writing commit messages"
    }
]

2. Generating commit messages: ✔
3. Choosing commit message: choosing...

 Please choice a commit message:
  [1] Docs(README): Configure OpenAI API key
  [2] Install(Composer): Add global and local install instructions
  [3] Usage(Commit Messages): Add best practices
 > 
```

### List commands

```shell
╰─ ./ai-commit list                                                                     ─╯

  
          _____    _____                          _ _   
    /\   |_   _|  / ____|                        (_) |  
   /  \    | |   | |     ___  _ __ ___  _ __ ___  _| |_ 
  / /\ \   | |   | |    / _ \| '_ ` _ \| '_ ` _ \| | __|
 / ____ \ _| |_  | |___| (_) | | | | | | | | | | | | |_ 
/_/    \_\_____|  \_____\___/|_| |_| |_|_| |_| |_|_|\__|
                                                        
                                                        

  1.1.2

  USAGE: ai-commit <command> [options] [arguments]

  commit      Automagically generate conventional commit messages with AI.
  completion  Dump the shell completion script
  config      Manage config options.
  self-update Allows to self-update a build application
  thanks      Thanks for using this tool.
```

### Operate config

```shell
./ai-commit config [set, get, unset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
./ai-commit config list
./ai-commit config edit
```

### Self update

```shell
╰─ ./ai-commit self-update                                                              ─╯

Checking for a new version...
=============================

                                                                                           
 [OK] Updated from version 1.1.1 to v1.1.2.                                                
                                                                                           
```

## Testing

```shell
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
