# ai-commit

<!-- https://looka.com -->
<p align="center"><img src="art/logo.png" alt="logo" style="width: 62%; height: 62%;"></p>

[简体中文](README-zh_CN.md) | [ENGLISH](README.md)

> Automagically generate conventional git commit message with AI. - 使用 AI 自动生成约定式 git 提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](https://packagist.org/packages/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](https://packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](https://packagist.org/packages/guanguans/ai-commit)

## Support

- [x] [Bito Cli](https://github.com/gitbito/CLI)
- [x] [ERNIE-Bot-turbo](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot-turbo)
- [x] [ERNIE-Bot](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot)
- [x] [OpenAI Chat](https://platform.openai.com/docs/api-reference/chat)
- [x] [OpenAI](https://platform.openai.com/docs/api-reference/completions)
- [ ] ...

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
composer global require guanguans/ai-commit --dev -v # global
composer require guanguans/ai-commit --dev -v # local
```

## Usage

### Quick start

```shell
./ai-commit config set generators.bito_cli.path bito-cli-path... --global # Config Bito Cli path(Optional)
./ai-commit config set generators.openai.api_key sk-... --global # Config OpenAI API key
./ai-commit config set generators.openai_chat.api_key sk-... --global # Config OpenAI API key

./ai-commit config set generator openai_chat --global # Config default generator(Optional)
./ai-commit commit # Generate and commit message
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

![](docs/ai-commit-vhs.gif)

### List commands

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

### Operate config

```shell
./ai-commit config [set, get, unset, reset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
./ai-commit config reset key
./ai-commit config list
./ai-commit config edit
```

### Self update

```shell
╰─ ./ai-commit self-update                                        ─╯

Checking for a new version...
=============================

                                                                     
 [OK] Updated from version 1.2.4 to v1.2.5.                          
                                                                     
```

### Command help

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
