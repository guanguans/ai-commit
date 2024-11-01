<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

<a name="unreleased"></a>
## [Unreleased]


<a name="1.15.0"></a>
## [1.15.0] - 2024-11-02
### Bug Fixes
- **config:** Update AI commit options for better performance

### Build
- **deps:** update laminas-servicemanager to 3.7.0

### CI
- **config:** Add readme-lint script to validate README files

### Docs
- add Traditional Chinese Readme
- **README:** Add GitHub Models CLI to documentation

### Features
- **generator:** Improve command options handling in GithubModelsCliGenerator
- **generators:** Add GithubModelsCliGenerator for command execution

### Pull Requests
- Merge pull request [#205](https://github.com/guanguans/ai-commit/issues/205) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#204](https://github.com/guanguans/ai-commit/issues/204) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.83.2
- Merge pull request [#203](https://github.com/guanguans/ai-commit/issues/203) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.83.0
- Merge pull request [#202](https://github.com/guanguans/ai-commit/issues/202) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#200](https://github.com/guanguans/ai-commit/issues/200) from PrathameshSPawar/main
- Merge pull request [#198](https://github.com/guanguans/ai-commit/issues/198) from samarth29jc/TW_Readme
- Merge pull request [#201](https://github.com/guanguans/ai-commit/issues/201) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#199](https://github.com/guanguans/ai-commit/issues/199) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#194](https://github.com/guanguans/ai-commit/issues/194) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#192](https://github.com/guanguans/ai-commit/issues/192) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.13
- Merge pull request [#191](https://github.com/guanguans/ai-commit/issues/191) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.14.5"></a>
## [1.14.5] - 2024-10-24
### Docs
- **readme:** update command examples for configuration

### Reverts
- chore(deps-dev): bump laminas/laminas-text from 2.9.0 to 2.11.0

### Pull Requests
- Merge pull request [#190](https://github.com/guanguans/ai-commit/issues/190) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#189](https://github.com/guanguans/ai-commit/issues/189) from guanguans/revert-188-dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#188](https://github.com/guanguans/ai-commit/issues/188) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#187](https://github.com/guanguans/ai-commit/issues/187) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.12
- Merge pull request [#185](https://github.com/guanguans/ai-commit/issues/185) from sravan1946/add-link-to-badge


<a name="1.14.4"></a>
## [1.14.4] - 2024-10-22
### Pull Requests
- Merge pull request [#186](https://github.com/guanguans/ai-commit/issues/186) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#184](https://github.com/guanguans/ai-commit/issues/184) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#183](https://github.com/guanguans/ai-commit/issues/183) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.11


<a name="1.14.3"></a>
## [1.14.3] - 2024-10-17
### Pull Requests
- Merge pull request [#182](https://github.com/guanguans/ai-commit/issues/182) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.14.2"></a>
## [1.14.2] - 2024-10-16
### Bug Fixes
- **commands:** fix handling of JSON decoding errors

### Code Refactoring
- **Generator:** Refactor Generator class
- **Generator.php:** refactor Generator class
- **app:** refactor CommitCommand.php and GithubCopilotCliGenerator.php
- **generator:** refactor processHelperMustRun and processHelperRun

### Performance Improvements
- **Generator.php:** improve process handling

### Pull Requests
- Merge pull request [#181](https://github.com/guanguans/ai-commit/issues/181) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#180](https://github.com/guanguans/ai-commit/issues/180) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.9


<a name="1.14.1"></a>
## [1.14.1] - 2024-10-15
### Code Refactoring
- **Generators:** refactor generate method in BitoCliGenerator
- **app:** Refactor Generator.php and GithubCopilotCliGenerator.php
- **generators:** improve process helper management
- **generators:** Refactor generator classes

### Features
- **Generator:** add logger property
- **generators:** Add processHelperMustRun and processHelperRun methods

### Tests
- **Generators:** add string cmd test
- **Generators:** Update Generator.php and GithubCopilotCliGenerator.php
- **Unit:** Add GithubCopilotCliGeneratorTest


<a name="1.14.0"></a>
## [1.14.0] - 2024-10-15
### Bug Fixes
- **Generators:** 修复 GithubCopilotCliGenerator 输出格式
- **Generators:** update GithubCopilotCliGenerator to include output message
- **app:** Fix dumping issue
- **app:** Fix copilot explain command output

### Code Refactoring
- **generators:** update CLI binary configuration

### Docs
- **commit:** Update dependencies in README files

### Features
- **generator:** Support for github copilot CLI driver

### Pull Requests
- Merge pull request [#179](https://github.com/guanguans/ai-commit/issues/179) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.13.5"></a>
## [1.13.5] - 2024-10-13
### Code Refactoring
- **BitoCliGenerator:** improve code structure and readability
- **logging:** replace NullLogger with ConsoleLogger for verbose output


<a name="1.13.4"></a>
## [1.13.4] - 2024-10-13
### Bug Fixes
- **composer-updater:** remove --ansi option from commands

### Code Refactoring
- **Generators:** update BitoCliGenerator to improve process handling

### Docs
- Move images to resources directory
- add Japanese README

### Pull Requests
- Merge pull request [#177](https://github.com/guanguans/ai-commit/issues/177) from eltociear/add-japanese-readme
- Merge pull request [#178](https://github.com/guanguans/ai-commit/issues/178) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.8
- Merge pull request [#176](https://github.com/guanguans/ai-commit/issues/176) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#175](https://github.com/guanguans/ai-commit/issues/175) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#174](https://github.com/guanguans/ai-commit/issues/174) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.7
- Merge pull request [#173](https://github.com/guanguans/ai-commit/issues/173) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#172](https://github.com/guanguans/ai-commit/issues/172) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.13.3"></a>
## [1.13.3] - 2024-10-01
### Code Refactoring
- **ConfigCommand:** simplify argToValue function

### Pull Requests
- Merge pull request [#171](https://github.com/guanguans/ai-commit/issues/171) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.13.2"></a>
## [1.13.2] - 2024-09-30
### Bug Fixes
- **tests:** remove commented parameters in InvalidJsons.php

### Docs
- update logo path and clean up files

### Performance Improvements
- **AppServiceProvider:** extend logger to use NullLogger
- **exceptions:** Improve error reporting behavior


<a name="1.13.1"></a>
## [1.13.1] - 2024-09-30
### Bug Fixes
- **Exceptions:** Correct error message formatting
- **deps:** update laminas and other dependencies

### Code Refactoring
- **exceptions:** simplify exception handling logic

### Performance Improvements
- **JsonFixer:** Improve readability of conditional statements
- **commands:** Improve null handling and code clarity

### Style
- **generators:** Refactor config property initialization

### Pull Requests
- Merge pull request [#170](https://github.com/guanguans/ai-commit/issues/170) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.6
- Merge pull request [#169](https://github.com/guanguans/ai-commit/issues/169) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#168](https://github.com/guanguans/ai-commit/issues/168) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.13.0"></a>
## [1.13.0] - 2024-09-26
### Features
- **command:** add dry-run option for commit messages
- **commit:** add optional diff parameter for commit command

### Pull Requests
- Merge pull request [#166](https://github.com/guanguans/ai-commit/issues/166) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.5
- Merge pull request [#165](https://github.com/guanguans/ai-commit/issues/165) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.3
- Merge pull request [#164](https://github.com/guanguans/ai-commit/issues/164) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.2
- Merge pull request [#163](https://github.com/guanguans/ai-commit/issues/163) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.82.1
- Merge pull request [#162](https://github.com/guanguans/ai-commit/issues/162) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.10
- Merge pull request [#161](https://github.com/guanguans/ai-commit/issues/161) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#160](https://github.com/guanguans/ai-commit/issues/160) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0


<a name="1.12.5"></a>
## [1.12.5] - 2024-08-16
### Build
- **deps:** update guzzlehttp/guzzle to version 7.9

### CI
- **rector:** Remove unused rector and update configurations

### Pull Requests
- Merge pull request [#159](https://github.com/guanguans/ai-commit/issues/159) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.9
- Merge pull request [#158](https://github.com/guanguans/ai-commit/issues/158) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.8
- Merge pull request [#157](https://github.com/guanguans/ai-commit/issues/157) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.7
- Merge pull request [#156](https://github.com/guanguans/ai-commit/issues/156) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.6
- Merge pull request [#155](https://github.com/guanguans/ai-commit/issues/155) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.5
- Merge pull request [#154](https://github.com/guanguans/ai-commit/issues/154) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.81.4
- Merge pull request [#153](https://github.com/guanguans/ai-commit/issues/153) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.5
- Merge pull request [#152](https://github.com/guanguans/ai-commit/issues/152) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.4
- Merge pull request [#151](https://github.com/guanguans/ai-commit/issues/151) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.3
- Merge pull request [#150](https://github.com/guanguans/ai-commit/issues/150) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.2
- Merge pull request [#149](https://github.com/guanguans/ai-commit/issues/149) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.9.2
- Merge pull request [#148](https://github.com/guanguans/ai-commit/issues/148) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.9.1
- Merge pull request [#147](https://github.com/guanguans/ai-commit/issues/147) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.1
- Merge pull request [#146](https://github.com/guanguans/ai-commit/issues/146) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.9.0
- Merge pull request [#144](https://github.com/guanguans/ai-commit/issues/144) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.80.0
- Merge pull request [#143](https://github.com/guanguans/ai-commit/issues/143) from guanguans/dependabot/composer/laravel-zero/phar-updater-1.4.0
- Merge pull request [#141](https://github.com/guanguans/ai-commit/issues/141) from guanguans/dependabot/composer/laminas/laminas-text-2.11.0
- Merge pull request [#140](https://github.com/guanguans/ai-commit/issues/140) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-2.2.0
- Merge pull request [#139](https://github.com/guanguans/ai-commit/issues/139) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.79.0


<a name="1.12.4"></a>
## [1.12.4] - 2024-06-24
### Bug Fixes
- **config:** Update logo font class conditionally

### CI
- **phpunit:** Fix coverage exclusion in phpunit.xml.dist
- **phpunit:** Remove coverage settings and add source settings


<a name="1.12.3"></a>
## [1.12.3] - 2024-06-24

<a name="1.12.2"></a>
## [1.12.2] - 2024-06-24
### Bug Fixes
- **app:** Remove outdated OpenAI model options

### Build
- **composer:** update composer-git-hooks version requirement

### Pull Requests
- Merge pull request [#137](https://github.com/guanguans/ai-commit/issues/137) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.78.2


<a name="1.12.1"></a>
## [1.12.1] - 2024-06-12
### Style
- **commands:** Remove unnecessary annotations


<a name="1.12.0"></a>
## [1.12.0] - 2024-06-12
### Docs
- **config:** Add commit type prompt in ai-commit.php

### Features
- add ability to select commit type
- **CommitCommand.php:** Modify commit type selection logic

### Performance Improvements
- **app:** improve handle method performance

### Tests
- **test:** improve commit command test coverage

### Pull Requests
- Merge pull request [#136](https://github.com/guanguans/ai-commit/issues/136) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.78.1


<a name="1.11.2"></a>
## [1.11.2] - 2024-06-11
### Pull Requests
- Merge pull request [#135](https://github.com/guanguans/ai-commit/issues/135) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.78.0
- Merge pull request [#134](https://github.com/guanguans/ai-commit/issues/134) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.77.0
- Merge pull request [#133](https://github.com/guanguans/ai-commit/issues/133) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.76.3
- Merge pull request [#132](https://github.com/guanguans/ai-commit/issues/132) from guanguans/dependabot/composer/mockery/mockery-1.6.12
- Merge pull request [#131](https://github.com/guanguans/ai-commit/issues/131) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.76.2
- Merge pull request [#130](https://github.com/guanguans/ai-commit/issues/130) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.76.1
- Merge pull request [#129](https://github.com/guanguans/ai-commit/issues/129) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.76.0
- Merge pull request [#127](https://github.com/guanguans/ai-commit/issues/127) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.75.1
- Merge pull request [#126](https://github.com/guanguans/ai-commit/issues/126) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.75.0
- Merge pull request [#125](https://github.com/guanguans/ai-commit/issues/125) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-2.1.0
- Merge pull request [#124](https://github.com/guanguans/ai-commit/issues/124) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.74.0
- Merge pull request [#123](https://github.com/guanguans/ai-commit/issues/123) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.73.0


<a name="1.11.1"></a>
## [1.11.1] - 2024-04-08
### Features
- **tests:** Add HTTP fake setup for Moonshot and chat completions

### Tests
- **Pest:** Update API response mock data
- **Unit:** Add MoonshotGeneratorTest.php and OpenAIGeneratorTest.php


<a name="1.11.0"></a>
## [1.11.0] - 2024-04-08
### Code Refactoring
- **generator:** Refactor GeneratorManager to improve readability and maintainability

### Features
- **Generators:** Add KimiGenerator class for AI completion
- **Kimi:** Add chatCompletions method to Kimi class


<a name="1.10.2"></a>
## [1.10.2] - 2024-04-07
### Pull Requests
- Merge pull request [#118](https://github.com/guanguans/ai-commit/issues/118) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-2.0.0
- Merge pull request [#122](https://github.com/guanguans/ai-commit/issues/122) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.72.0
- Merge pull request [#121](https://github.com/guanguans/ai-commit/issues/121) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.71.2
- Merge pull request [#120](https://github.com/guanguans/ai-commit/issues/120) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.71.1
- Merge pull request [#119](https://github.com/guanguans/ai-commit/issues/119) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.71.0
- Merge pull request [#117](https://github.com/guanguans/ai-commit/issues/117) from guanguans/dependabot/composer/mockery/mockery-1.6.11
- Merge pull request [#116](https://github.com/guanguans/ai-commit/issues/116) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.70.3
- Merge pull request [#114](https://github.com/guanguans/ai-commit/issues/114) from guanguans/dependabot/composer/mockery/mockery-1.6.10
- Merge pull request [#113](https://github.com/guanguans/ai-commit/issues/113) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.70.2
- Merge pull request [#111](https://github.com/guanguans/ai-commit/issues/111) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.70.0


<a name="1.10.1"></a>
## [1.10.1] - 2024-03-14

<a name="1.10.0"></a>
## [1.10.0] - 2024-03-14
### Code Refactoring
- **openai:** Improve data handling and response creation

### Docs
- **StrMacro:** Add link to polyfill-php83

### Features
- **composer-updater:** add composer-updater script and configuration

### Pull Requests
- Merge pull request [#109](https://github.com/guanguans/ai-commit/issues/109) from guanguans/dependabot/composer/mockery/mockery-1.6.9
- Merge pull request [#108](https://github.com/guanguans/ai-commit/issues/108) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.69.0
- Merge pull request [#107](https://github.com/guanguans/ai-commit/issues/107) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.68.5
- Merge pull request [#94](https://github.com/guanguans/ai-commit/issues/94) from guanguans/dependabot/github_actions/codecov/codecov-action-4
- Merge pull request [#106](https://github.com/guanguans/ai-commit/issues/106) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.68.4
- Merge pull request [#105](https://github.com/guanguans/ai-commit/issues/105) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.68.3
- Merge pull request [#104](https://github.com/guanguans/ai-commit/issues/104) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.68.2
- Merge pull request [#103](https://github.com/guanguans/ai-commit/issues/103) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.68.0
- Merge pull request [#102](https://github.com/guanguans/ai-commit/issues/102) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.7
- Merge pull request [#101](https://github.com/guanguans/ai-commit/issues/101) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.6
- Merge pull request [#100](https://github.com/guanguans/ai-commit/issues/100) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.10.0
- Merge pull request [#99](https://github.com/guanguans/ai-commit/issues/99) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.5
- Merge pull request [#98](https://github.com/guanguans/ai-commit/issues/98) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.4
- Merge pull request [#97](https://github.com/guanguans/ai-commit/issues/97) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.2
- Merge pull request [#96](https://github.com/guanguans/ai-commit/issues/96) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.67.1
- Merge pull request [#95](https://github.com/guanguans/ai-commit/issues/95) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.66.3
- Merge pull request [#93](https://github.com/guanguans/ai-commit/issues/93) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.66.2
- Merge pull request [#92](https://github.com/guanguans/ai-commit/issues/92) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.66.1
- Merge pull request [#88](https://github.com/guanguans/ai-commit/issues/88) from guanguans/dependabot/github_actions/actions/cache-4
- Merge pull request [#84](https://github.com/guanguans/ai-commit/issues/84) from guanguans/imgbot
- Merge pull request [#91](https://github.com/guanguans/ai-commit/issues/91) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.65.0
- Merge pull request [#90](https://github.com/guanguans/ai-commit/issues/90) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.64.0
- Merge pull request [#89](https://github.com/guanguans/ai-commit/issues/89) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.11
- Merge pull request [#87](https://github.com/guanguans/ai-commit/issues/87) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.10
- Merge pull request [#86](https://github.com/guanguans/ai-commit/issues/86) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.9
- Merge pull request [#85](https://github.com/guanguans/ai-commit/issues/85) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.8


<a name="1.9.4"></a>
## [1.9.4] - 2024-01-03

<a name="1.9.3"></a>
## [1.9.3] - 2024-01-03
### Bug Fixes
- **Commands:** Fix issue in CommitCommand
- **commands:** Fix formatting issue in CommitCommand

### Code Refactoring
- **CommitCommand:** refactor commit message generation and confirmation
- **OpenAIGenerator:** Improve data handling
- **commands:** update commit message generation
- **commit:** refactor CommitCommand.php


<a name="1.9.2"></a>
## [1.9.2] - 2024-01-03
### Bug Fixes
- **CommitCommand:** add newLine(3) before and after confirming commit message
- **commands:** Automagically generate conventional commit message with AI

### Code Refactoring
- **CommitCommand:** Refactor CommitCommand class

### Features
- **commands:** add new line before and after commit tasks


<a name="1.9.1"></a>
## [1.9.1] - 2024-01-02
### Pull Requests
- Merge pull request [#74](https://github.com/guanguans/ai-commit/issues/74) from guanguans/dependabot/github_actions/actions/labeler-5
- Merge pull request [#76](https://github.com/guanguans/ai-commit/issues/76) from guanguans/dependabot/github_actions/actions/stale-9
- Merge pull request [#79](https://github.com/guanguans/ai-commit/issues/79) from guanguans/dependabot/github_actions/actions/upload-artifact-4


<a name="1.9.0"></a>
## [1.9.0] - 2023-12-25
### Pull Requests
- Merge pull request [#83](https://github.com/guanguans/ai-commit/issues/83) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.7
- Merge pull request [#82](https://github.com/guanguans/ai-commit/issues/82) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.6
- Merge pull request [#81](https://github.com/guanguans/ai-commit/issues/81) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.5
- Merge pull request [#80](https://github.com/guanguans/ai-commit/issues/80) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.4
- Merge pull request [#78](https://github.com/guanguans/ai-commit/issues/78) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.3
- Merge pull request [#77](https://github.com/guanguans/ai-commit/issues/77) from guanguans/dependabot/composer/mockery/mockery-1.6.7
- Merge pull request [#75](https://github.com/guanguans/ai-commit/issues/75) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.2
- Merge pull request [#73](https://github.com/guanguans/ai-commit/issues/73) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.8.1
- Merge pull request [#72](https://github.com/guanguans/ai-commit/issues/72) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.9.0
- Merge pull request [#71](https://github.com/guanguans/ai-commit/issues/71) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.1
- Merge pull request [#70](https://github.com/guanguans/ai-commit/issues/70) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.63.0


<a name="1.8.6"></a>
## [1.8.6] - 2023-11-16

<a name="1.8.5"></a>
## [1.8.5] - 2023-11-13
### Code Refactoring
- **monorepo-builder:** update CreateGithubReleaseWorker to CreateGithubReleaseReleaseWorker


<a name="1.8.4"></a>
## [1.8.4] - 2023-11-09

<a name="1.8.3"></a>
## [1.8.3] - 2023-11-01
### Pull Requests
- Merge pull request [#69](https://github.com/guanguans/ai-commit/issues/69) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.62.1
- Merge pull request [#68](https://github.com/guanguans/ai-commit/issues/68) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.8.0
- Merge pull request [#67](https://github.com/guanguans/ai-commit/issues/67) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.62.0
- Merge pull request [#66](https://github.com/guanguans/ai-commit/issues/66) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.60.4
- Merge pull request [#65](https://github.com/guanguans/ai-commit/issues/65) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.60.3
- Merge pull request [#64](https://github.com/guanguans/ai-commit/issues/64) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.60.2
- Merge pull request [#60](https://github.com/guanguans/ai-commit/issues/60) from guanguans/dependabot/github_actions/stefanzweifel/git-auto-commit-action-5
- Merge pull request [#63](https://github.com/guanguans/ai-commit/issues/63) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.60.1
- Merge pull request [#62](https://github.com/guanguans/ai-commit/issues/62) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.60.0
- Merge pull request [#59](https://github.com/guanguans/ai-commit/issues/59) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.59.0
- Merge pull request [#58](https://github.com/guanguans/ai-commit/issues/58) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.58.0
- Merge pull request [#57](https://github.com/guanguans/ai-commit/issues/57) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.57.0
- Merge pull request [#54](https://github.com/guanguans/ai-commit/issues/54) from guanguans/dependabot/github_actions/codecov/codecov-action-4
- Merge pull request [#51](https://github.com/guanguans/ai-commit/issues/51) from guanguans/dependabot/github_actions/actions/checkout-4
- Merge pull request [#56](https://github.com/guanguans/ai-commit/issues/56) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.56.1
- Merge pull request [#55](https://github.com/guanguans/ai-commit/issues/55) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.56.0
- Merge pull request [#53](https://github.com/guanguans/ai-commit/issues/53) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.55.1
- Merge pull request [#52](https://github.com/guanguans/ai-commit/issues/52) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.54.4
- Merge pull request [#50](https://github.com/guanguans/ai-commit/issues/50) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.54.3
- Merge pull request [#49](https://github.com/guanguans/ai-commit/issues/49) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.54.2
- Merge pull request [#48](https://github.com/guanguans/ai-commit/issues/48) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.54.1
- Merge pull request [#47](https://github.com/guanguans/ai-commit/issues/47) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.8.0
- Merge pull request [#46](https://github.com/guanguans/ai-commit/issues/46) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.54.0
- Merge pull request [#45](https://github.com/guanguans/ai-commit/issues/45) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.53.0


<a name="1.8.2"></a>
## [1.8.2] - 2023-08-18

<a name="1.8.1"></a>
## [1.8.1] - 2023-08-18
### Bug Fixes
- **Support:** Sanitize data in Ernie and OpenAI

### Code Refactoring
- **generator:** simplify buildWriter method
- **generator:** Change visibility of ernie property
- **rector:** apply rector
- **rector:** apply rector

### Docs
- **readme:** Update ERNIE-Bot links

### Features
- **tests:** Add ErnieTest.php
- **tests.Unit.Generators.ErnieBotGeneratorTest:** add test for generating commit messages

### Tests
- **ErnieBotGeneratorTest:** add test for writer function

### Pull Requests
- Merge pull request [#44](https://github.com/guanguans/ai-commit/issues/44) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.52.1


<a name="1.8.0"></a>
## [1.8.0] - 2023-08-17
### Code Refactoring
- **Support:** move sanitizeData method to FoundationSDK.php
- **Support:** refactor Ernie class
- **app:** improve GeneratorManager

### Docs
- **readme:** update broken links in README.md

### Features
- **Ernie:** Add Ernie class
- **Generators:** Add ErnieBotGenerator
- **generators:** add ErnieBotTurbo generator

### Pull Requests
- Merge pull request [#43](https://github.com/guanguans/ai-commit/issues/43) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.50.0
- Merge pull request [#42](https://github.com/guanguans/ai-commit/issues/42) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.48.0
- Merge pull request [#41](https://github.com/guanguans/ai-commit/issues/41) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.47.0
- Merge pull request [#40](https://github.com/guanguans/ai-commit/issues/40) from guanguans/dependabot/composer/mockery/mockery-1.6.6
- Merge pull request [#39](https://github.com/guanguans/ai-commit/issues/39) from guanguans/dependabot/composer/mockery/mockery-1.6.5
- Merge pull request [#38](https://github.com/guanguans/ai-commit/issues/38) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.46.3
- Merge pull request [#37](https://github.com/guanguans/ai-commit/issues/37) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.46.2
- Merge pull request [#36](https://github.com/guanguans/ai-commit/issues/36) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.46.1
- Merge pull request [#35](https://github.com/guanguans/ai-commit/issues/35) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.45.3
- Merge pull request [#34](https://github.com/guanguans/ai-commit/issues/34) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.45.2
- Merge pull request [#33](https://github.com/guanguans/ai-commit/issues/33) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.45.1
- Merge pull request [#32](https://github.com/guanguans/ai-commit/issues/32) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.45.0


<a name="1.7.6"></a>
## [1.7.6] - 2023-07-25
### Code Refactoring
- **configcommand:** update process TTY option
- **tests:** Remove unnecessary code in ConfigCommandTest

### Tests
- **ConfigCommandTest:** Skip windows_os on edit config test


<a name="1.7.5"></a>
## [1.7.5] - 2023-07-25
### Bug Fixes
- **tests:** Fix skipping of test cases

### Tests
- **CommitCommandTest:** add --no-verify option


<a name="1.7.4"></a>
## [1.7.4] - 2023-07-25
### Bug Fixes
- **monorepo-builder:** Update autoload.php path
- **tests:** Fix skipping tests on non-Windows OS

### Code Refactoring
- **ConfigCommand:** use dependency injection for ExecutableFinder
- **commands:** Update ConfigCommand.php
- **tests:** Refactor `find` method in `ExecutableFinder`

### Docs
- **config:** Improve commit message guidelines

### Features
- **ConfigCommand:** Add support for windows editors

### Pull Requests
- Merge pull request [#31](https://github.com/guanguans/ai-commit/issues/31) from guanguans/dependabot/composer/guanguans/monorepo-builder-worker-1.1.11


<a name="1.7.3"></a>
## [1.7.3] - 2023-07-24
### Code Refactoring
- **CommitCommand:** add shouldVerify method

### Docs
- **README:** Update command help section


<a name="1.7.2"></a>
## [1.7.2] - 2023-07-24
### Bug Fixes
- **composer:** update ai-commit-no-verify script

### Code Refactoring
- **CommitCommand:** change edit mode handling

### Features
- **commands:** add --no-verify option


<a name="1.7.1"></a>
## [1.7.1] - 2023-07-24
### Code Refactoring
- **Commands:** Use note instead of info for command line output
- **rector.php:** remove unused PHPUnit sets

### Features
- **CommitCommand:** Add option to regenerate commit


<a name="1.7.0"></a>
## [1.7.0] - 2023-07-23
### Build
- **workflows:** Add support for manual tag input

### Code Refactoring
- **rector:** Remove unused rules and dependencies

### Features
- **composer:** add guanguans/monorepo-builder-worker dependency


<a name="v1.6.9"></a>
## [v1.6.9] - 2023-07-14
### Bug Fixes
- **commands:** Update success message in ConfigCommand


<a name="v1.6.8"></a>
## [v1.6.8] - 2023-07-14
### Bug Fixes
- **commands:** Update commit success message

### Features
- **composer.json:** add ai-commit-no-verify command

### Pull Requests
- Merge pull request [#30](https://github.com/guanguans/ai-commit/issues/30) from guanguans/dependabot/composer/pestphp/pest-1.23.1
- Merge pull request [#29](https://github.com/guanguans/ai-commit/issues/29) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.44.0
- Merge pull request [#28](https://github.com/guanguans/ai-commit/issues/28) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.43.0


<a name="v1.6.7"></a>
## [v1.6.7] - 2023-06-30
### Code Refactoring
- **CommitCommand:** Change visibility of methods


<a name="v1.6.6"></a>
## [v1.6.6] - 2023-06-30
### Pull Requests
- Merge pull request [#27](https://github.com/guanguans/ai-commit/issues/27) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.42.0
- Merge pull request [#26](https://github.com/guanguans/ai-commit/issues/26) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-1.6.0
- Merge pull request [#25](https://github.com/guanguans/ai-commit/issues/25) from guanguans/dependabot/github_actions/trufflesecurity/trufflehog-3.41.1
- Merge pull request [#24](https://github.com/guanguans/ai-commit/issues/24) from guanguans/imgbot


<a name="v1.6.5"></a>
## [v1.6.5] - 2023-06-26
### Features
- **README:** add logo
- **composer:** Add trufflehog command
- **github-actions:** add secrets check workflow


<a name="v1.6.4"></a>
## [v1.6.4] - 2023-06-23
### Bug Fixes
- **build:** change directory permission to 0755


<a name="v1.6.3"></a>
## [v1.6.3] - 2023-06-23
### Bug Fixes
- **commands:** update error message for no cached files to commit

### Code Refactoring
- **app:** improve code readability and structure
- **commit-command:** improve code readability and add doc comments


<a name="v1.6.2"></a>
## [v1.6.2] - 2023-06-22
### Bug Fixes
- **CommitCommand:** use --cached instead of --staged in git diff

### Code Refactoring
- **ConfigManager:** use File facade and update return types
- **commands:** modify CommitCommand to handle Windows OS
- **config:** update generator name to openai_chat
- **generators:** rename Bito to Bito Cli

### Docs
- **README:** reorganize and update installation steps

### Features
- **app:** add ChangeAndIfToEarlyReturnRector to ConfigManager and CommitCommand
- **tests:** add support for Windows in tests

### Tests
- **ConfigCommandTest:** add skip for windows_os()
- **config:** add test for edit config on Windows


<a name="v1.6.1"></a>
## [v1.6.1] - 2023-06-21
### Docs
- **readme:** Update README with OpenAI and Bito configuration instructions
- **readme:** add support section

### Features
- **composer.json:** add bito and other dependencies

### Tests
- **generators:** Add BitoGeneratorTest


<a name="v1.6.0"></a>
## [v1.6.0] - 2023-06-21
### Bug Fixes
- **generator:** use configured path for bito command

### Code Refactoring
- **Generators:** make BitoGenerator class final and change visibility of properties
- **Generators:** simplify code for generating prompt file
- **app:** use custom exceptions and update driver creation
- **app:** update GeneratorManager.php
- **composer:** remove unused composer script

### Features
- **config:** add Bito driver configuration


<a name="v1.5.3"></a>
## [v1.5.3] - 2023-06-21
### Code Refactoring
- **OpenAIGenerator:** change extractCompletion method to non-static

### Features
- **rector:** add FinalizeClassesWithoutChildrenRector


<a name="v1.5.2"></a>
## [v1.5.2] - 2023-06-21
### Features
- **dev:** Add bamarni/composer-bin-plugin to require-dev


<a name="v1.5.1"></a>
## [v1.5.1] - 2023-06-20

<a name="v1.5.0"></a>
## [v1.5.0] - 2023-06-20
### Pull Requests
- Merge pull request [#23](https://github.com/guanguans/ai-commit/issues/23) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.7.2
- Merge pull request [#22](https://github.com/guanguans/ai-commit/issues/22) from guanguans/dependabot/composer/mockery/mockery-1.6.2
- Merge pull request [#21](https://github.com/guanguans/ai-commit/issues/21) from guanguans/dependabot/composer/mockery/mockery-1.6.0
- Merge pull request [#20](https://github.com/guanguans/ai-commit/issues/20) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-1.5.1
- Merge pull request [#19](https://github.com/guanguans/ai-commit/issues/19) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.7.0
- Merge pull request [#18](https://github.com/guanguans/ai-commit/issues/18) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-1.5.0


<a name="v1.4.4"></a>
## [v1.4.4] - 2023-05-16
### Pull Requests
- Merge pull request [#17](https://github.com/guanguans/ai-commit/issues/17) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.6.1


<a name="v1.4.3"></a>
## [v1.4.3] - 2023-05-06
### Features
- **config:** Update app locale to English


<a name="v1.4.2"></a>
## [v1.4.2] - 2023-05-04
### Code Refactoring
- **commands:** use collection to set options

### Features
- **config:** add reset action to ConfigCommand

### Pull Requests
- Merge pull request [#16](https://github.com/guanguans/ai-commit/issues/16) from guanguans/dependabot/composer/pestphp/pest-1.23.0
- Merge pull request [#15](https://github.com/guanguans/ai-commit/issues/15) from guanguans/dependabot/github_actions/dependabot/fetch-metadata-1.4.0
- Merge pull request [#14](https://github.com/guanguans/ai-commit/issues/14) from guanguans/dependabot/composer/guzzlehttp/guzzle-7.5.1


<a name="v1.4.1"></a>
## [v1.4.1] - 2023-04-13

<a name="v1.4.0"></a>
## [v1.4.0] - 2023-04-13

<a name="v1.3.3"></a>
## [v1.3.3] - 2023-04-01
### Pull Requests
- Merge pull request [#13](https://github.com/guanguans/ai-commit/issues/13) from guanguans/dependabot/github_actions/actions/stale-8
- Merge pull request [#12](https://github.com/guanguans/ai-commit/issues/12) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.7.1
- Merge pull request [#11](https://github.com/guanguans/ai-commit/issues/11) from guanguans/dependabot/composer/pestphp/pest-1.22.6
- Merge pull request [#10](https://github.com/guanguans/ai-commit/issues/10) from guanguans/dependabot/composer/php-mock/php-mock-phpunit-2.7.0


<a name="v1.3.2"></a>
## [v1.3.2] - 2023-03-06
### Bug Fixes
- **generator:** fix JSON decoding error


<a name="v1.3.1"></a>
## [v1.3.1] - 2023-03-04
### Pull Requests
- Merge pull request [#9](https://github.com/guanguans/ai-commit/issues/9) from guanguans/dependabot/composer/pestphp/pest-1.22.5


<a name="v1.3.0"></a>
## [v1.3.0] - 2023-03-03
### Bug Fixes
- **config:** update generator name

### Pull Requests
- Merge pull request [#8](https://github.com/guanguans/ai-commit/issues/8) from guanguans/imgbot


<a name="v1.2.8"></a>
## [v1.2.8] - 2023-03-02

<a name="v1.2.7"></a>
## [v1.2.7] - 2023-03-01

<a name="v1.2.6"></a>
## [v1.2.6] - 2023-02-28

<a name="v1.2.5"></a>
## [v1.2.5] - 2023-02-28
### Bug Fixes
- **Generator:** Hydrate data with JSON_THROW_ON_ERROR

### Code Refactoring
- **OpenAIGenerator:** Refactor response handling


<a name="v1.2.4"></a>
## [v1.2.4] - 2023-02-22

<a name="v1.2.3"></a>
## [v1.2.3] - 2023-02-20

<a name="v1.2.2"></a>
## [v1.2.2] - 2023-02-18

<a name="v1.2.1"></a>
## [v1.2.1] - 2023-02-18

<a name="v1.2.0"></a>
## [v1.2.0] - 2023-02-17

<a name="v1.1.5"></a>
## [v1.1.5] - 2023-02-16

<a name="v1.1.4"></a>
## [v1.1.4] - 2023-02-16

<a name="v1.1.3"></a>
## [v1.1.3] - 2023-02-15

<a name="v1.1.2"></a>
## [v1.1.2] - 2023-02-15

<a name="v1.1.1"></a>
## [v1.1.1] - 2023-02-15

<a name="v1.1.0"></a>
## [v1.1.0] - 2023-02-15

<a name="v1.0.4"></a>
## [v1.0.4] - 2023-02-15
### Pull Requests
- Merge pull request [#5](https://github.com/guanguans/ai-commit/issues/5) from guanguans/imgbot


<a name="v1.0.3"></a>
## [v1.0.3] - 2023-02-15

<a name="v1.0.2"></a>
## [v1.0.2] - 2023-02-15

<a name="v1.0.1"></a>
## [v1.0.1] - 2023-02-15
### Bug Fixes
- **ConfigCommand:** Fix UnsupportedActionOfConfigException


<a name="v1.0.0"></a>
## [v1.0.0] - 2023-02-09
### Bug Fixes
- **OpenAIGenerator:** Fix is_json() call

### Code Refactoring
- Apply rector

### Docs
- Update README and CHANGELOG


<a name="v0.5.0"></a>
## [v0.5.0] - 2023-02-09
### Code Refactoring
- **ConfigCommand:** Transform config value

### Features
- **CommitCommand:** Add config option
- **CommitCommand:** Add InputArgument for path
- **ConfigCommand:** Add ACTIONS constant
- **config:** Add list actions


<a name="v0.4.0"></a>
## [v0.4.0] - 2023-02-08
### Features
- **CommitCommand:** Add no-edit option
- **StrMacro:** Add StrMacro class


<a name="v0.3.0"></a>
## [v0.3.0] - 2023-02-07

<a name="v0.2.5"></a>
## [v0.2.5] - 2023-02-07

<a name="v0.2.4"></a>
## [v0.2.4] - 2023-02-07

<a name="v0.2.3"></a>
## [v0.2.3] - 2023-02-07

<a name="v0.2.2"></a>
## [v0.2.2] - 2023-02-07

<a name="v0.2.1"></a>
## [v0.2.1] - 2023-02-07

<a name="v0.2.0"></a>
## [v0.2.0] - 2023-02-06
### Features
- add config command


<a name="v0.1.0"></a>
## v0.1.0 - 2023-02-03
### Features
- add config manager


[Unreleased]: https://github.com/guanguans/ai-commit/compare/1.15.0...HEAD
[1.15.0]: https://github.com/guanguans/ai-commit/compare/1.14.5...1.15.0
[1.14.5]: https://github.com/guanguans/ai-commit/compare/1.14.4...1.14.5
[1.14.4]: https://github.com/guanguans/ai-commit/compare/1.14.3...1.14.4
[1.14.3]: https://github.com/guanguans/ai-commit/compare/1.14.2...1.14.3
[1.14.2]: https://github.com/guanguans/ai-commit/compare/1.14.1...1.14.2
[1.14.1]: https://github.com/guanguans/ai-commit/compare/1.14.0...1.14.1
[1.14.0]: https://github.com/guanguans/ai-commit/compare/1.13.5...1.14.0
[1.13.5]: https://github.com/guanguans/ai-commit/compare/1.13.4...1.13.5
[1.13.4]: https://github.com/guanguans/ai-commit/compare/1.13.3...1.13.4
[1.13.3]: https://github.com/guanguans/ai-commit/compare/1.13.2...1.13.3
[1.13.2]: https://github.com/guanguans/ai-commit/compare/1.13.1...1.13.2
[1.13.1]: https://github.com/guanguans/ai-commit/compare/1.13.0...1.13.1
[1.13.0]: https://github.com/guanguans/ai-commit/compare/1.12.5...1.13.0
[1.12.5]: https://github.com/guanguans/ai-commit/compare/1.12.4...1.12.5
[1.12.4]: https://github.com/guanguans/ai-commit/compare/1.12.3...1.12.4
[1.12.3]: https://github.com/guanguans/ai-commit/compare/1.12.2...1.12.3
[1.12.2]: https://github.com/guanguans/ai-commit/compare/1.12.1...1.12.2
[1.12.1]: https://github.com/guanguans/ai-commit/compare/1.12.0...1.12.1
[1.12.0]: https://github.com/guanguans/ai-commit/compare/1.11.2...1.12.0
[1.11.2]: https://github.com/guanguans/ai-commit/compare/1.11.1...1.11.2
[1.11.1]: https://github.com/guanguans/ai-commit/compare/1.11.0...1.11.1
[1.11.0]: https://github.com/guanguans/ai-commit/compare/1.10.2...1.11.0
[1.10.2]: https://github.com/guanguans/ai-commit/compare/1.10.1...1.10.2
[1.10.1]: https://github.com/guanguans/ai-commit/compare/1.10.0...1.10.1
[1.10.0]: https://github.com/guanguans/ai-commit/compare/1.9.4...1.10.0
[1.9.4]: https://github.com/guanguans/ai-commit/compare/1.9.3...1.9.4
[1.9.3]: https://github.com/guanguans/ai-commit/compare/1.9.2...1.9.3
[1.9.2]: https://github.com/guanguans/ai-commit/compare/1.9.1...1.9.2
[1.9.1]: https://github.com/guanguans/ai-commit/compare/1.9.0...1.9.1
[1.9.0]: https://github.com/guanguans/ai-commit/compare/1.8.6...1.9.0
[1.8.6]: https://github.com/guanguans/ai-commit/compare/1.8.5...1.8.6
[1.8.5]: https://github.com/guanguans/ai-commit/compare/1.8.4...1.8.5
[1.8.4]: https://github.com/guanguans/ai-commit/compare/1.8.3...1.8.4
[1.8.3]: https://github.com/guanguans/ai-commit/compare/1.8.2...1.8.3
[1.8.2]: https://github.com/guanguans/ai-commit/compare/1.8.1...1.8.2
[1.8.1]: https://github.com/guanguans/ai-commit/compare/1.8.0...1.8.1
[1.8.0]: https://github.com/guanguans/ai-commit/compare/1.7.6...1.8.0
[1.7.6]: https://github.com/guanguans/ai-commit/compare/1.7.5...1.7.6
[1.7.5]: https://github.com/guanguans/ai-commit/compare/1.7.4...1.7.5
[1.7.4]: https://github.com/guanguans/ai-commit/compare/1.7.3...1.7.4
[1.7.3]: https://github.com/guanguans/ai-commit/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/guanguans/ai-commit/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/guanguans/ai-commit/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/guanguans/ai-commit/compare/v1.6.9...1.7.0
[v1.6.9]: https://github.com/guanguans/ai-commit/compare/v1.6.8...v1.6.9
[v1.6.8]: https://github.com/guanguans/ai-commit/compare/v1.6.7...v1.6.8
[v1.6.7]: https://github.com/guanguans/ai-commit/compare/v1.6.6...v1.6.7
[v1.6.6]: https://github.com/guanguans/ai-commit/compare/v1.6.5...v1.6.6
[v1.6.5]: https://github.com/guanguans/ai-commit/compare/v1.6.4...v1.6.5
[v1.6.4]: https://github.com/guanguans/ai-commit/compare/v1.6.3...v1.6.4
[v1.6.3]: https://github.com/guanguans/ai-commit/compare/v1.6.2...v1.6.3
[v1.6.2]: https://github.com/guanguans/ai-commit/compare/v1.6.1...v1.6.2
[v1.6.1]: https://github.com/guanguans/ai-commit/compare/v1.6.0...v1.6.1
[v1.6.0]: https://github.com/guanguans/ai-commit/compare/v1.5.3...v1.6.0
[v1.5.3]: https://github.com/guanguans/ai-commit/compare/v1.5.2...v1.5.3
[v1.5.2]: https://github.com/guanguans/ai-commit/compare/v1.5.1...v1.5.2
[v1.5.1]: https://github.com/guanguans/ai-commit/compare/v1.5.0...v1.5.1
[v1.5.0]: https://github.com/guanguans/ai-commit/compare/v1.4.4...v1.5.0
[v1.4.4]: https://github.com/guanguans/ai-commit/compare/v1.4.3...v1.4.4
[v1.4.3]: https://github.com/guanguans/ai-commit/compare/v1.4.2...v1.4.3
[v1.4.2]: https://github.com/guanguans/ai-commit/compare/v1.4.1...v1.4.2
[v1.4.1]: https://github.com/guanguans/ai-commit/compare/v1.4.0...v1.4.1
[v1.4.0]: https://github.com/guanguans/ai-commit/compare/v1.3.3...v1.4.0
[v1.3.3]: https://github.com/guanguans/ai-commit/compare/v1.3.2...v1.3.3
[v1.3.2]: https://github.com/guanguans/ai-commit/compare/v1.3.1...v1.3.2
[v1.3.1]: https://github.com/guanguans/ai-commit/compare/v1.3.0...v1.3.1
[v1.3.0]: https://github.com/guanguans/ai-commit/compare/v1.2.8...v1.3.0
[v1.2.8]: https://github.com/guanguans/ai-commit/compare/v1.2.7...v1.2.8
[v1.2.7]: https://github.com/guanguans/ai-commit/compare/v1.2.6...v1.2.7
[v1.2.6]: https://github.com/guanguans/ai-commit/compare/v1.2.5...v1.2.6
[v1.2.5]: https://github.com/guanguans/ai-commit/compare/v1.2.4...v1.2.5
[v1.2.4]: https://github.com/guanguans/ai-commit/compare/v1.2.3...v1.2.4
[v1.2.3]: https://github.com/guanguans/ai-commit/compare/v1.2.2...v1.2.3
[v1.2.2]: https://github.com/guanguans/ai-commit/compare/v1.2.1...v1.2.2
[v1.2.1]: https://github.com/guanguans/ai-commit/compare/v1.2.0...v1.2.1
[v1.2.0]: https://github.com/guanguans/ai-commit/compare/v1.1.5...v1.2.0
[v1.1.5]: https://github.com/guanguans/ai-commit/compare/v1.1.4...v1.1.5
[v1.1.4]: https://github.com/guanguans/ai-commit/compare/v1.1.3...v1.1.4
[v1.1.3]: https://github.com/guanguans/ai-commit/compare/v1.1.2...v1.1.3
[v1.1.2]: https://github.com/guanguans/ai-commit/compare/v1.1.1...v1.1.2
[v1.1.1]: https://github.com/guanguans/ai-commit/compare/v1.1.0...v1.1.1
[v1.1.0]: https://github.com/guanguans/ai-commit/compare/v1.0.4...v1.1.0
[v1.0.4]: https://github.com/guanguans/ai-commit/compare/v1.0.3...v1.0.4
[v1.0.3]: https://github.com/guanguans/ai-commit/compare/v1.0.2...v1.0.3
[v1.0.2]: https://github.com/guanguans/ai-commit/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/guanguans/ai-commit/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/guanguans/ai-commit/compare/v0.5.0...v1.0.0
[v0.5.0]: https://github.com/guanguans/ai-commit/compare/v0.4.0...v0.5.0
[v0.4.0]: https://github.com/guanguans/ai-commit/compare/v0.3.0...v0.4.0
[v0.3.0]: https://github.com/guanguans/ai-commit/compare/v0.2.5...v0.3.0
[v0.2.5]: https://github.com/guanguans/ai-commit/compare/v0.2.4...v0.2.5
[v0.2.4]: https://github.com/guanguans/ai-commit/compare/v0.2.3...v0.2.4
[v0.2.3]: https://github.com/guanguans/ai-commit/compare/v0.2.2...v0.2.3
[v0.2.2]: https://github.com/guanguans/ai-commit/compare/v0.2.1...v0.2.2
[v0.2.1]: https://github.com/guanguans/ai-commit/compare/v0.2.0...v0.2.1
[v0.2.0]: https://github.com/guanguans/ai-commit/compare/v0.1.0...v0.2.0
