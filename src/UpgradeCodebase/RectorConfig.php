<?php

namespace Enox\UpgradeCodebase;

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\Rector\ClassMethod\ReplaceArgumentDefaultValueRector;
use Rector\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector;
use Rector\Arguments\Rector\MethodCall\RemoveMethodCallParamRector;
use Rector\Assert\Rector\ClassMethod\AddAssertArrayFromClassMethodDocblockRector;
use Rector\Carbon\Rector\FuncCall\DateFuncCallToCarbonRector;
use Rector\Carbon\Rector\FuncCall\TimeFuncCallToCarbonRector;
use Rector\Carbon\Rector\MethodCall\DateTimeMethodCallToCarbonRector;
use Rector\Carbon\Rector\New_\DateTimeInstanceToCarbonRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Set\ValueObject\SetList;


/**
 * Create Rector configuration with all rules and settings
 */
function createRectorConfig(): RectorConfigBuilder
{
    $config = ArgumentsMapper::map();

    return RectorConfig::configure()
        ->withPaths([$config['path']])

        // PHP version upgrade sets
        ->withSets([
            SetList::PHP_72,  // Starting point
            SetList::PHP_73,
            SetList::PHP_74,
            SetList::PHP_80,
            SetList::PHP_81,
            SetList::PHP_82,
        ])

        // Code quality improvements
        ->withSets([
            SetList::CODE_QUALITY,
            SetList::CODING_STYLE,
            SetList::DEAD_CODE,
            SetList::PRIVATIZATION,
            SetList::TYPE_DECLARATION,
            SetList::EARLY_RETURN,
            SetList::INSTANCEOF,
        ])

        // Additional configurations
        ->withParallel(300)  // Parallel processing
        ->withMemoryLimit('8G')
        ->withImportNames(false, false, false)  // Keep imports as-is
        ->withSkip([])

        // Custom rules for specific scenarios (only rules not included in sets)
        ->withRules([
            // Arguments rules (not in standard sets)
            ArgumentAdderRector::class,
            ReplaceArgumentDefaultValueRector::class,
            FunctionArgumentDefaultValueReplacerRector::class,
            RemoveMethodCallParamRector::class,

            // Assert rules (not in standard sets)
            AddAssertArrayFromClassMethodDocblockRector::class,

            // Carbon rules (not in standard sets)
            DateFuncCallToCarbonRector::class,
            TimeFuncCallToCarbonRector::class,
            DateTimeMethodCallToCarbonRector::class,
            DateTimeInstanceToCarbonRector::class,

            // Coding Style rules (only those not in CODING_STYLE set)
            StaticArrowFunctionRector::class,
            StaticClosureRector::class,
            EncapsedStringsToSprintfRector::class,

            // Naming rules (not in standard sets)
            RenameVariableToMatchMethodCallReturnTypeRector::class,
            RenameParamToMatchTypeRector::class,
            RenameVariableToMatchNewTypeRector::class,
            RenamePropertyToMatchTypeRector::class,
            RenameForeachValueVariableToMatchExprVariableRector::class,
        ])

        // Configure file extensions
        ->withFileExtensions(['php'])

        // Configure autoloading
        ->withAutoloadPaths([
            $config['path'] . 'vendor/autoload.php',
        ]);
}


return createRectorConfig();
