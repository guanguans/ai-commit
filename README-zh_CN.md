# ai-commit

![](docs/ai-commit.gif)

[简体中文](README-zh_CN.md) | [ENGLISH](README.md)

> Automagically generate conventional commit messages with AI. - 使用 AI 自动生约定式提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](//packagist.org/packages/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](//packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](//packagist.org/packages/guanguans/ai-commit)
![GitHub repo size](https://img.shields.io/github/repo-size/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)

## 环境要求

* PHP >= 7.2

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

### 配置 [OpenAI API key](https://platform.openai.com/account/api-keys)

```shell
./ai-commit config set generators.openai.api_key sk-... --global
```

### 生成且提交信息

```shell
./ai-commit commit
```

```shell
╰─ ./ai-commit commit                                                                                  ─╯
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

![](docs/ai-commit.gif)

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
