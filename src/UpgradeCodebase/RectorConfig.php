<?php

namespace Enox\UpgradeCodebase;

use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
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
            SetList::PHP_73,
            SetList::PHP_74,
            SetList::PHP_80,
            SetList::PHP_81,
            SetList::PHP_82,
        ])
        // Additional configurations
        //->withParallel(16)  // Parallel processing
        ->withMemoryLimit('8G')
        ->withImportNames(false, false, false)  // Keep imports as-is to avoid noisy git diffs
        ->withSkip([])

        // Custom rules for specific scenarios (only rules not included in sets)
        ->withRules([])

        // Configure file extensions
        ->withFileExtensions(['php', 'inc'])

        // Configure autoloading
        ->withAutoloadPaths($config['autoloadPath']
            ? [$config['autoloadPath']]
            : []);
}


return createRectorConfig();
