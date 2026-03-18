#!/usr/bin/env php
<?php

/**
 * Enox - PHP Codebase Upgrade Tool
 *
 * Using Rector with extensive rule sets and optimizations.
 * Using PhpStan and PHPCompatibility to run compatibility reports
 */

require_once __DIR__ . '/vendor/autoload.php';

use Enox\AnalyzeCompatibility\Analyze;
use Enox\UpgradeCodebase\ArgumentsMapper;
use Enox\UsageManual\UsageManual;

class EnoxClient
{
    private array $argv;

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public function run(): void
    {
        if ($this->hasHelpFlag()) {
            $this->showUsage();
            exit(0);
        }

        if (count($this->argv) < 2) {
            $this->showUsage();
            exit(1);
        }

        $action = $this->parseAction();

        if (empty($action)) {
            echo "\n ❌ Action is empty\n\n";
            $this->showUsage();
            exit(1);
        }
        switch (strtolower($action)) {
            case 'upgrade':
                echo "Starting Rector upgrade...\n";
                $arguments = ArgumentsMapper::map();

                if (empty($arguments['path'])) {
                    echo "Error: Path is required. Use --path=<directory>\n";
                    exit(1);
                }

                echo "Starting PHP upgrade \n";
                echo "Target path: {$arguments['path']}\n";
                echo "Dry run: " . ($arguments['dryRun'] ? 'Yes' : 'No') . "\n\n";

                $command = [
                        'RECTOR_PATH=' . escapeshellarg($arguments['path']),
                        'RECTOR_AUTOLOAD_PATH=' . escapeshellarg($arguments['autoloadPath']),
                        'php8.2 vendor/bin/rector process',
                        '--config src/UpgradeCodebase/RectorConfig.php',
                        '--ansi'
                ];

                if ($arguments['dryRun']) {
                    $command[] = '--dry-run';
                }

                passthru(implode(' ', $command), $resultCode);

                if ($resultCode === 0) {
                    echo "\n✅ Rector completed successfully.\n";
                } else {
                    echo "\n❌ Rector failed with exit code: $resultCode\n";
                    exit($resultCode);
                }
                break;

            case 'analyze':
                echo "Starting compatibility analysis...\n";

                try {
                    $analyze = new Analyze();
                    $analyze->run();

                    $summary = $analyze->getSummary();
                    if ($summary) {
                        echo "\n✅ Analysis completed successfully!\n";
                        echo "📊 Summary:\n";
                        echo "  - PHPStan errors: " . ($summary['phpstan']['total_errors'] ?? 0) . "\n";
                        echo "  - PHPCompatibility issues: " . ($summary['phpcompatibility']['total_issues'] ?? 0) . "\n";
                        echo "  - Reports saved to: " . ($summary['config']['analysis_timestamp'] ?? 'unknown') . "\n";
                    }
                } catch (Exception $e) {
                    echo "\n❌ Analysis failed: " . $e->getMessage() . "\n";
                    exit(1);
                }
                break;

            default:
                echo "❌ Unknown action: {$action}\n\n";
                $this->showUsage();
                exit(1);
        }
    }

    private function hasHelpFlag(): bool
    {
        return in_array('--help', $this->argv) || in_array('-h', $this->argv);
    }

    private function parseAction(): string
    {
        foreach ($this->argv as $arg) {
            if (str_starts_with($arg, '--do=')) {
                return substr($arg, 5);
            }
        }

        return '';
    }

    private function showUsage(): void
    {
        echo UsageManual::show();
    }
}

// Run the CLI application
if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}


$cli = new EnoxClient($argv);
$cli->run();
