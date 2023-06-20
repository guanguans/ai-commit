<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\Class_\AddArrayDefaultToArrayPropertyRector;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryAndToEarlyReturnRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\EarlyReturn\Rector\StmtsAwareInterface\ReturnEarlyIfVariableRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\PHPUnit\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\Rector\MethodCall\RemoveExpectAnyFromMockRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Strict\Rector\Ternary\DisallowedShortTernaryRuleFixerRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);
    // $rectorConfig->disableParallel();
    $rectorConfig->parallel(300);
    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.neon');
    $rectorConfig->phpVersion(PhpVersion::PHP_73);
    // $rectorConfig->cacheClass(FileCacheStorage::class);
    // $rectorConfig->cacheDirectory(__DIR__.'/build/rector');
    // $rectorConfig->containerCacheDirectory(__DIR__.'/build/rector');
    // $rectorConfig->disableParallel();
    // $rectorConfig->fileExtensions(['php']);
    // $rectorConfig->indent(' ', 4);
    // $rectorConfig->memoryLimit('2G');
    // $rectorConfig->nestedChainMethodCallLimit(3);
    // $rectorConfig->noDiffs();
    // $rectorConfig->parameters()->set(Option::APPLY_AUTO_IMPORT_NAMES_ON_CHANGED_FILES_ONLY, true);
    // $rectorConfig->removeUnusedImports();

    $rectorConfig->bootstrapFiles([
        // __DIR__.'/vendor/autoload.php',
    ]);

    $rectorConfig->autoloadPaths([
        // __DIR__.'/vendor/autoload.php',
    ]);

    $rectorConfig->paths([
        __DIR__.'/app',
        // __DIR__.'/tests',
        __DIR__.'/.*.php',
        __FILE__,
    ]);

    $rectorConfig->skip([
        // rules
        // AddArrayDefaultToArrayPropertyRector::class,
        // AddDefaultValueForUndefinedVariableRector::class,
        // AddSeeTestAnnotationRector::class,
        // CallableThisArrayToAnonymousFunctionRector::class,
        // ChangeAndIfToEarlyReturnRector::class,
        ExplicitBoolCompareRector::class,
        // RemoveEmptyClassMethodRector::class,
        // RemoveUnusedVariableAssignRector::class,
        // ReturnBinaryOrToEarlyReturnRector::class,
        // SimplifyBoolIdenticalTrueRector::class,
        // StaticClosureRector::class,
        // UnSpreadOperatorRector::class,

        EncapsedStringsToSprintfRector::class,
        UnSpreadOperatorRector::class,
        InlineIfToExplicitIfRector::class,
        LogicalToBooleanRector::class,
        RenameParamToMatchTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        // NewlineAfterStatementRector::class,
        // ReturnBinaryAndToEarlyReturnRector::class,
        // VarConstantCommentRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,

        // DisallowedEmptyRuleFixerRector::class => [
        //     __DIR__.'/src/Support/QueryAnalyzer.php',
        // ],
        // RemoveExtraParametersRector::class => [
        //     __DIR__.'/src/Macros/QueryBuilderMacro.php',
        // ],
        // ExplicitBoolCompareRector::class => [
        //     __DIR__.'/src/JavascriptRenderer.php',
        // ],
        // RenameForeachValueVariableToMatchExprVariableRector::class => [
        //     __DIR__.'/src/OutputManager.php',
        // ],
        CompleteDynamicPropertiesRector::class => [
            __DIR__.'/app/Support/FoundationSDK.php',
        ],
        StaticClosureRector::class => [
            __DIR__.'/tests',
        ],
        JsonThrowOnErrorRector::class => [
            __DIR__.'/app/Generators/OpenAIGenerator.php',
        ],
        DisallowedShortTernaryRuleFixerRector::class => [
            __DIR__.'/app/Support/FoundationSDK.php',
        ],
        // RemoveExpectAnyFromMockRector::class => [
        //     __DIR__.'/tests/Concerns/WithDumpableTest.php',
        // ],
        // ReturnEarlyIfVariableRector::class => [
        //     __DIR__.'/src/Support/EscapeArg.php',
        // ],

        // paths
        __DIR__.'/tests/AspectMock',
        '**/Fixture*',
        '**/Fixture/*',
        '**/Fixtures*',
        '**/Fixtures/*',
        '**/Stub*',
        '**/Stub/*',
        '**/Stubs*',
        '**/Stubs/*',
        '**/Source*',
        '**/Source/*',
        '**/Expected/*',
        '**/Expected*',
        '**/__snapshots__/*',
        '**/__snapshots__*',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_73,
        SetList::PHP_73,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        // SetList::STRICT_BOOLEANS,
        // SetList::GMAGICK_TO_IMAGICK,
        // SetList::MYSQL_TO_MYSQLI,
        SetList::NAMING,
        // SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,

        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_91,
        // PHPUnitSetList::PHPUNIT80_DMS,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_EXCEPTION,
        PHPUnitSetList::REMOVE_MOCKS,
        PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, [
        'test' => 'it',
    ]);
};
