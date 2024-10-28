# ai-commit

[//]: # (<p align="center"><img src="resources/docs/logo.png" alt="logo" style="width: 62%; height: 62%;"></p>)
<p align="center"><img src="resources/docs/ai-commit-vhs.gif" alt="ai-commit-vhs"></p>

[简体中文](README-zh_CN.md) | [ENGLISH](README.md) | [日本語](README-ja.md) | [繁體中文](README-zh_TW.md)

> Automagically generate conventional git commit message with AI. - 使用 AI 自動生成約定式 git 提交訊息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](https://packagist.org/packages/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](https://packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](https://packagist.org/packages/guanguans/ai-commit)

## 支援

- [x] [Bito Cli](https://github.com/gitbito/CLI)
- [x] [ERNIE-Bot-turbo](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot-turbo)
- [x] [ERNIE-Bot](https://cloud.baidu.com/doc/WENXINWORKSHOP/s/Nlks5zkzu#ernie-bot)
- [x] [GitHub Copilot CLI](https://github.com/github/gh-copilot)
- [x] [Moonshot](https://platform.moonshot.cn/docs/api-reference)
- [x] [OpenAI Chat](https://platform.openai.com/docs/api-reference/chat)
- [x] [OpenAI](https://platform.openai.com/docs/api-reference/completions)
- [ ] ...

## 系統需求

* PHP >= 7.3

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

```shell
./ai-commit config set generators.bito_cli.binary bito-cli-binary... --global # 設定 Bito cli 執行檔（可選）
./ai-commit config set generators.ernie_bot.api_key api-key... --global # 設定 Ernie API 金鑰
./ai-commit config set generators.ernie_bot_turbo.api_key api-key... --global # 設定 Ernie API 金鑰
./ai-commit config set generators.github_copilot_cli.binary gh-cli-binary... --global # 設定 Github cli 執行檔（可選）
./ai-commit config set generators.moonshot.api_key sk-... --global # 設定 Moonshot API 金鑰
./ai-commit config set generators.openai.api_key sk-... --global # 設定 OpenAI API 金鑰
./ai-commit config set generators.openai_chat.api_key sk-... --global # 設定 OpenAI API 金鑰

./ai-commit config set generator openai_chat --global # 設定預設生成器（可選）
./ai-commit commit # 使用預設生成器產生並提交訊息
./ai-commit commit --generator=github_copilot_cli # 使用指定的生成器產生並提交訊息
```

```shell
╰─ ./ai-commit commit --generator=openai_chat --no-edit --no-verify --ansi                                           ─╯
1. 產生提交訊息中：正在產生...
{
    "subject": "chore(ai-commit): update settings and commands",
    "body": "- Set Width to 1200\n- Set Height to 742\n- Set TypingSpeed to 10ms\n- Set PlaybackSpeed to 0.2\n- Update git commands and sleep times"
}
1. 產生提交訊息：✔

2. 確認提交訊息：確認中...
+------------------------------------------------+---------------------------------------+
| subject                                        | body                                  |
+------------------------------------------------+---------------------------------------+
| chore(ai-commit): update settings and commands | - Set Width to 1200                   |
|                                                | - Set Height to 742                   |
|                                                | - Set TypingSpeed to 10ms             |
|                                                | - Set PlaybackSpeed to 0.2            |
|                                                | - Update git commands and sleep times |
+------------------------------------------------+---------------------------------------+

 是否要提交此訊息？(yes/no) [yes]:
 > 

2. 確認提交訊息：✔

3. 提交訊息中：提交中...

3. 提交訊息：✔

                                                                                                                        
 [OK] 成功產生並提交訊息。                                                                    
```

![](docs/ai-commit-vhs.gif)

### 指令列表

```shell
╰─ ./ai-commit list                                                     ─╯

  
          _____    _____                          _ _   
    /\   |_   _|  / ____|                        (_) |  
   /  \    | |   | |     ___  _ __ ___  _ __ ___  _| |_ 
  / /\ \   | |   | |    / _ \| '_ ` _ \| '_ ` _ \| | __|
 / ____ \ _| |_  | |___| (_) | | | | | | | | | | | | |_ 
/_/    \_\_____|  \_____\___/|_| |_| |_|_| |_| |_|_|\__|
                                                        
                                                        

  1.2.5

  使用方式：ai-commit <command> [options] [arguments]

  commit      使用 AI 自動生成約定式提交訊息。
  completion  輸出 shell 自動完成腳本
  config      管理設定選項。
  self-update 允許自我更新建置應用程式
  thanks      感謝使用此工具。
```

### 設定操作

```shell
./ai-commit config [set, get, unset, reset, list, edit] key value --global

./ai-commit config set key value
./ai-commit config get key
./ai-commit config unset key
./ai-commit config reset key
./ai-commit config list
./ai-commit config edit
```

### 自動更新

```shell
╰─ ./ai-commit self-update                                        ─╯

檢查新版本...
=============================

                                                                     
 [OK] 已從版本 1.2.4 更新至 v1.2.5。                          
                                                                     
```

### 指令說明

```shell
╰─ ./ai-commit commit --help                                                                                                               ─╯
描述：
  使用 AI 自動生成約定式提交訊息。

用法：
  commit [options] [--] [<path>]

參數：
  path                                   工作目錄 [預設值："/Users/yaozm/Documents/develop/ai-commit"]

選項：
      --commit-options[=COMMIT-OPTIONS]  為 `git commit` 指令附加選項 [預設值：["--edit"]] (可多值)
      --diff-options[=DIFF-OPTIONS]      為 `git diff` 指令附加選項 [預設值：[":!*-lock.json",":!*.lock",":!*.sum"]] (可多值)
  -g, --generator=GENERATOR              指定生成器名稱 [預設值："openai_chat"]
  -p, --prompt=PROMPT                    指定訊息生成的提示名稱 [預設值："conventional"]
      --no-edit                          啟用或停用 git commit `--no-edit` 選項
      --no-verify                        啟用或停用 git commit `--no-verify` 選項
  -c, --config[=CONFIG]                  指定設定檔
      --retry-times=RETRY-TIMES          指定重試次數 [預設值：3]
      --retry-sleep=RETRY-SLEEP          指定重試間隔毫秒數 [預設值：200]
      --dry-run                          僅生成訊息而不提交
      --diff[=DIFF]                      指定差異內容
  -h, --help                             顯示指定指令的說明。若未指定指令則顯示 list 指令的說明
  -q, --quiet                            不輸出任何訊息
  -V, --version                          顯示此應用程式版本
      --ansi|--no-ansi                   強制（或停用 --no-ansi）ANSI 輸出
  -n, --no-interaction                   不詢問任何互動問題
      --env[=ENV]                        指令應在其下執行的環境
  -v|vv|vvv, --verbose                   增加訊息的詳細程度：1 為正常輸出，2 為較詳細輸出，3 為偵錯輸出
```

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
