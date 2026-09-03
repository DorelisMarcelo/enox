<?php

namespace Enox\AnalyzeCompatibility;

use RuntimeException;

class Analyze
{
    private array $config;
    private string $reportsPath;
    private string $analysisTimestamp;

    public function __construct()
    {
        $this->config = array_merge([
            'path' => '',
            'autoloadPath' => '', // autoloader path to the project that you are analyzing
            'phpstanLevel' => 5,
            'targetPhpVersion' => '8.2',
            'outputFormat' => 'json',
            'bleedingEdge' => true,
            'memoryLimit' => '8G',
            'analysisName' => date('Y-m-d_H-i-s'), // Default timestamp
        ], ArgumentsMapper::map());


        $this->analysisTimestamp = $this->config['analysisName'];
        $this->reportsPath = $this->createReportsDirectory();
    }

    /**
     * Run complete compatibility analysis
     */
    public function run(): void
    {
        echo "Starting PHP Compatibility Analysis...\n";
        echo "Target path: {$this->config['path']}\n";
        echo "PHPStan level: {$this->config['phpstanLevel']}\n";
        echo "Target PHP version: {$this->config['targetPhpVersion']}\n\n";

        echo "🔍 Starting PHP Compatibility Analysis\n";
        echo "=====================================\n\n";

        $this->validatePath();

        // Check if directory will be cleaned and notify user
        $reportsDir = __DIR__ . '/../../analysisCompatibilityResult/' . $this->analysisTimestamp;
        if (is_dir($reportsDir) && (scandir($reportsDir) !== false && count(scandir($reportsDir)) > 2)) {
            echo "🧹 Cleaning existing analysis directory: {$this->analysisTimestamp}\n";
        }

        $this->saveConfiguration();

        echo "Analyzing path: {$this->config['path']}\n";
        echo "PHPStan level: {$this->config['phpstanLevel']}\n";
        echo "Target PHP version: {$this->config['targetPhpVersion']}\n";
        echo "Reports will be saved to: {$this->reportsPath}\n\n";

        // Run PHPStan analysis
        //$this->runPhpStanAnalysis();

        // Run PHPCompatibility analysis
        $this->runPhpCompatibilityAnalysis();

        echo "\n✅ Analysis completed successfully!\n";
        echo "📁 Reports available at: {$this->reportsPath}\n";
    }

    /**
     * Create reports directory structure
     */
    private function createReportsDirectory(): string
    {
        $baseDir = __DIR__ . '/../../analysisCompatibilityResult';
        $reportsDir = $baseDir . '/' . $this->analysisTimestamp;

        // Create base directory if it doesn't exist
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        // Clean the specific analysis directory if it exists and is not empty
        if (is_dir($reportsDir)) {
            $this->cleanDirectory($reportsDir);
        } else {
            mkdir($reportsDir, 0755, true);
        }

        return $reportsDir;
    }

    /**
     * Clean directory contents recursively
     */
    private function cleanDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                // Recursively clean subdirectory
                $this->cleanDirectory($path);
                rmdir($path);
            } else {
                // Remove file
                unlink($path);
            }
        }
    }

    /**
     * Validate the analysis path
     */
    private function validatePath(): void
    {
        if (empty($this->config['path'])) {
            echo "Error: Path is required. Use --path=<directory>\n";
            exit(1);
        }

        if (!is_dir($this->config['path']) && !is_file($this->config['path'])) {
            echo "Error: Directory '{$this->config['path']}' does not exist.\n";
            exit(1);
        }
    }

    /**
     * Save analysis configuration
     */
    private function saveConfiguration(): void
    {
        $configData = [
            'analysis_timestamp' => $this->analysisTimestamp,
            'analyzed_path' => realpath($this->config['path']),
            'phpstan_level' => $this->config['phpstanLevel'],
            'target_php_version' => $this->config['targetPhpVersion'],
            'output_format' => $this->config['outputFormat'],
            'memory_limit' => $this->config['memoryLimit'],
            'analysis_date' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'tools_used' => [
                'phpstan' => '2.1',
                'php_compatibility' => '9.3'
            ]
        ];

        file_put_contents(
            $this->reportsPath . '/config.json',
            json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Run PHPStan analysis
     */
    private function runPhpStanAnalysis(): void
    {
        echo "🔬 Running PHPStan analysis...\n";

        // Create temporary PHPStan configuration
        $phpstanConfig = $this->createPhpStanConfig();

        // Prepare PHPStan command
        $enoxVendorPath = __DIR__ . '/../../vendor/bin/phpstan';
        $command = [
            'php',
            '-d', 'memory_limit=' . $this->config['memoryLimit'],
            $enoxVendorPath,
            'analyse',
            $this->config['path'],
            '--configuration=' . $phpstanConfig,
            '--level=' . $this->config['phpstanLevel'],
            '--no-progress',
            '--error-format=json'
        ];

        // Execute PHPStan
        $output = $this->executeCommand($command);

        // Save PHPStan results
        $this->savePhpStanResults($output);

        echo "✅ PHPStan analysis completed\n";
    }

    /**
     * Create temporary PHPStan configuration
     */
    private function createPhpStanConfig(): string
    {
        $config = [
            'parameters' => [
                'paths' => [$this->config['path']],
                'level' => (int)$this->config['phpstanLevel'],
                'tmpDir' => $this->reportsPath . '/phpstan-cache',
                'excludePaths' => [
                    '*/vendor/*',
                    '*/node_modules/*',
                ],
                'ignoreErrors' => [
                    // Add common ignores if needed
                ],
            ]
        ];

        if (!empty($this->config['autoloadPath'])) {
            $config['parameters']['bootstrapFiles'] = [$this->config['autoloadPath']];
        }

        $configFile = $this->reportsPath . '/phpstan.neon';
        file_put_contents($configFile, $this->arrayToNeon($config));

        return $configFile;
    }

    /**
     * Run PHPCompatibility analysis using PHP_CodeSniffer
     */
    private function runPhpCompatibilityAnalysis(): void
    {
        echo "🔬 Running PHPCompatibility analysis...\n";

        // Prepare PHPCompatibility command
        $command = [
            'php',
            'vendor/bin/phpcs',
            '--standard=PHPCompatibility',
            '--runtime-set', 'testVersion', $this->config['targetPhpVersion'],
            '--report=json',
            '--report-file=' . $this->reportsPath . '/phpcompatibility.json',
            '--extensions=php',
            '--ignore=*/vendor/*,*/node_modules/*',
            $this->config['path']
        ];

        // Execute PHPCompatibility
        $output = $this->executeCommand($command);

        // Process and filter the results
        if (file_exists($this->reportsPath . '/phpcompatibility.json')) {
            $data = json_decode(file_get_contents($this->reportsPath . '/phpcompatibility.json'), true);

            if ($data !== null) {
                // Filter out files with no errors or warnings
                $filteredFiles = [];
                $totalErrors = 0;
                $totalWarnings = 0;

                foreach ($data['files'] ?? [] as $file => $fileData) {
                    $fileErrors = (int)$fileData['errors'];
                    $fileWarnings = (int)$fileData['warnings'];

                    if ($fileErrors > 0 || $fileWarnings > 0) {
                        $filteredFiles[$file] = $fileData;
                        $totalErrors += $fileErrors;
                        $totalWarnings += $fileWarnings;
                    }
                }

                // Only save if there are files with issues
                if (!empty($filteredFiles)) {
                    $filteredData = [
                        'totals' => [
                            'errors' => $totalErrors,
                            'warnings' => $totalWarnings,
                            'fixable' => $data['totals']['fixable'] ?? 0,
                        ],
                        'files' => $filteredFiles
                    ];

                    file_put_contents(
                        $this->reportsPath . '/phpcompatibility.json',
                        json_encode($filteredData)
                    );

                    // Save raw output for reference
                    file_put_contents(
                        $this->reportsPath . '/phpcompatibility_raw.txt',
                        $output
                    );
                } else {
                    // Remove the JSON file and create success indicator with funny message
                    unlink($this->reportsPath . '/phpcompatibility.json');

                    $funnyMessages = [
                        "🎉 Yeyy no compatibility issues! Your code is timeless! Or... is it from the future? 🚀",
                        "🦄 PHP compatibility perfection!",
                        "🏆 Zero warnings and errors! Your code works on all PHP versions! Magic! ✨",
                        "🎯 Perfect compatibility! Time to celebrate... or check if you actually tested anything! 😅",
                        "🌟 No issues found! Your code is PHP version agnostic! 🪄",
                        "🚀 Compatibility master! Ready for the target PHP version! Probably! 🤞",
                        "🎨 Flawless compatibility! Are you secretly the PHP core team? 🤔",
                        "💎 Zero compatibility issues! Your code is immortal! Or just empty! 🤷‍♂️",
                        "💎 Zero errors found! I can't believe it, you must be an AI agent! 🤷‍♂️",
                    ];

                    $message = $funnyMessages[array_rand($funnyMessages)];

                    file_put_contents(
                        $this->reportsPath . '/phpcompatibility_success.txt',
                        $message . "\n" .
                        "Analysis date: " . date('Y-m-d H:i:s') . "\n" .
                        "Target PHP version: " . $this->config['targetPhpVersion'] . "\n" .
                        "Files analyzed: " . count($data['files'] ?? []) . "\n"
                    );
                }
            } else {
                // Invalid JSON, save raw output for debugging
                file_put_contents(
                    $this->reportsPath . '/phpcompatibility_raw.txt',
                    $output
                );
            }
        } else {
            // Command failed, save raw output for debugging
            file_put_contents(
                $this->reportsPath . '/phpcompatibility_error.txt',
                "PHPCompatibility analysis failed to generate report.\n" .
                "Raw output:\n" . $output
            );
        }

        echo "✅ PHPCompatibility analysis completed\n";
    }

    /**
     * Execute command and capture output
     */
    private function executeCommand(array $command): string
    {
        $commandString = implode(' ', array_map('escapeshellarg', $command));

        $descriptor = [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        echo "\n\nRunning command: $commandString \n\n";

        $process = proc_open($commandString, $descriptor, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException("Failed to execute command: {$commandString}");
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0 && !empty($stderr)) {
            echo "Warning: Command exited with code {$exitCode}: {$stderr}\n";
        }

        return $stdout;
    }

    /**
     * Save PHPStan results
     */
    private function savePhpStanResults(string $output): void
    {
        $data = json_decode($output, true);

        if(empty($data)) {
            echo "❌ PHPStan result is empty. Analysis aborted\n";
            exit(1);
        }

        if (!empty($data['errors'])) {
            // Invalid JSON, save raw output for debugging
            file_put_contents($this->reportsPath . '/phpstan_invalid.json', $data['errors']);
            echo "❌ PHPStan runtime fatal errors. Analysis aborted\n";
            foreach ($data['errors'] as $error) {
                echo $error . "\n";
            }
            exit(1);
        }

        // Filter out files with no errors
        $filteredFiles = [];
        $totalErrors = 0;
        $filesWithErrors = 0;

        foreach ($data['files'] ?? [] as $file => $fileData) {
            $fileErrors = count($fileData['messages'] ?? []);
            if ($fileErrors > 0) {
                $filteredFiles[$file] = $fileData;
                $totalErrors += $fileErrors;
                $filesWithErrors++;
            }
        }

        // Only save if there are files with errors
        if (!empty($filteredFiles)) {
            $filteredData = [
                'totals' => [
                    'file_errors' => $totalErrors,
                    'files' => $filesWithErrors,
                ],
                'files' => $filteredFiles
            ];

            file_put_contents($this->reportsPath . '/phpstan.json', json_encode($filteredData));

            // Create summary
            $summary = [
                'total_files' => count($data['files'] ?? []),
                'total_errors' => $totalErrors,
                'files_with_errors' => $filesWithErrors,
            ];

            file_put_contents(
                $this->reportsPath . '/phpstan_summary.json',
                json_encode($summary, JSON_PRETTY_PRINT)
            );
        } else {
            // Create a success indicator file with funny message
            $funnyMessages = [
                "🎉 Yeyy no errors found! The code is good! Or... is it? 🤔",
                "🦄 Sparkling clean code! Are you sure you're a human developer? ✨",
                "🏆 Zero errors! You've achieved developer enlightenment! 🧘‍♂️",
                "🎯 Perfect code! Time to celebrate... or worry about what you missed! 😅",
                "🌟 No errors detected! Your code is officially magical!. 🪄 Not applied if you are a Muggle",
                "🚀 Clean code alert! You're ready for production! Maybe! 🤞",
                "🎨 Zero issues! Your code is a work of art! Or it's too simple! 🤷‍♂️",
                "💎 Flawless code! Are you secretly a code-review robot? 🤖"
            ];

            $message = $funnyMessages[array_rand($funnyMessages)];

            file_put_contents(
                $this->reportsPath . '/phpstan_success.txt',
                $message . "\n" .
                "Analysis date: " . date('Y-m-d H:i:s') . "\n" .
                "Files analyzed: " . count($data['files'] ?? []) . "\n" .
                "PHPStan level: " . $this->config['phpstanLevel'] . "\n"
            );
        }
    }

    /**
     * Convert array to NEON format (simplified)
     */
    private function arrayToNeon(array $array, int $indent = 0): string
    {
        $neon = '';
        $indentStr = str_repeat('  ', $indent);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $neon .= $indentStr . $key . ":\n";
                $neon .= $this->arrayToNeon($value, $indent + 1);
            } elseif (is_string($value)) {
                $neon .= $indentStr . $key . ': ' . $value . "\n";
            } else {
                $neon .= $indentStr . $key . ': ' . var_export($value, true) . "\n";
            }
        }

        return $neon;
    }

    /**
     * Get analysis results summary
     */
    public function getSummary(): array
    {
        $configFile = $this->reportsPath . '/config.json';

        $summary = [
            'config' => json_decode(file_get_contents($configFile), true) ?? [],
            'phpstan' => [],
            'phpcompatibility' => [],
        ];

        // Check PHPStan results
        $phpstanSummaryFile = $this->reportsPath . '/phpstan_summary.json';
        $phpstanSuccessFile = $this->reportsPath . '/phpstan_success.txt';

        if (file_exists($phpstanSummaryFile)) {
            $summary['phpstan'] = json_decode(file_get_contents($phpstanSummaryFile), true) ?? [];
        } elseif (file_exists($phpstanSuccessFile)) {
            $summary['phpstan'] = [
                'total_files' => 0,
                'total_errors' => 0,
                'files_with_errors' => 0,
                'status' => 'success'
            ];
        }

        // Check PHPCompatibility results
        $phpCompatFile = $this->reportsPath . '/phpcompatibility.json';
        $phpCompatSuccessFile = $this->reportsPath . '/phpcompatibility_success.txt';

        if (file_exists($phpCompatFile)) {
            $phpCompatData = json_decode(file_get_contents($phpCompatFile), true);
            if ($phpCompatData) {
                $summary['phpcompatibility'] = [
                    'total_files' => count($phpCompatData['files'] ?? []),
                    'total_issues' => ($phpCompatData['totals']['errors'] ?? 0) + ($phpCompatData['totals']['warnings'] ?? 0),
                    'errors' => $phpCompatData['totals']['errors'] ?? 0,
                    'warnings' => $phpCompatData['totals']['warnings'] ?? 0,
                ];
            }
        } elseif (file_exists($phpCompatSuccessFile)) {
            $summary['phpcompatibility'] = [
                'total_files' => 0,
                'total_issues' => 0,
                'errors' => 0,
                'warnings' => 0,
                'status' => 'success'
            ];
        }

        return $summary;
    }
}
