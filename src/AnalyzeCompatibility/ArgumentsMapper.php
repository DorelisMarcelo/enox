<?php

namespace Enox\AnalyzeCompatibility;

class ArgumentsMapper
{
    /**
     * Parse command line arguments and return configuration
     */
    public static function map(): array
    {
        global $argv;

        $config = [
            'path' => getenv('ANALYSIS_PATH') ?: '',
            'autoloadPath' => getenv('ANALYSIS_ROOT_PATH') ?: '',
            'phpstanLevel' => 5,
            'targetPhpVersion' => '8.2',
            'outputFormat' => 'json',
            'memoryLimit' => '8G',
            'analysisName' => getenv('ANALYSIS_NAME') ?: null, // null to use default timestamp
        ];

        for ($i = 0; $i < count($argv); $i++) {
            $argExploded = explode('=', $argv[$i]);
            $key = reset($argExploded);
            $value = end($argExploded);

            switch ($key) {
                case '--path':
                case '-p':
                    $config['path'] = $value;
                    break;
                case '--autoload-path':
                case '-a':
                    $config['autoloadPath'] = $value;
                    break;
                case '--phpstan-level':
                case '-l':
                    $config['phpstanLevel'] = (int)$value;
                    break;
                case '--target-php':
                case '-t':
                    $config['targetPhpVersion'] = $value;
                    break;
                case '--memory-limit':
                case '-m':
                    $config['memoryLimit'] = $value;
                    break;
                case '--output-format':
                case '-f':
                    $config['outputFormat'] = $value;
                    break;
                case '--analysis-name':
                case '-n':
                    $config['analysisName'] = $value;
                    break;
            }
        }

        if (empty($config['path'])) {
            echo "Error: Path is required. Use --path <directory> or -p <directory>\n";
            exit(1);
        }

        if (!is_dir($config['path'])) {
            echo "Error: Directory '{$config['path']}' does not exist.\n";
            exit(1);
        }

        // Validate root path if specified
        if (!empty($config['autoloadPath']) && !is_file($config['autoloadPath'])) {
            echo "Error: Root directory '{$config['autoloadPath']}' does not exist.\n";
            exit(1);
        }

        // Validate PHPStan level
        if ($config['phpstanLevel'] < 0 || $config['phpstanLevel'] > 9) {
            echo "Error: PHPStan level must be between 0 and 9.\n";
            exit(1);
        }

        // Validate PHP version
        $validPhpVersions = ['7.2', '7.3', '7.4', '8.0', '8.1', '8.2', '8.3'];
        if (!in_array($config['targetPhpVersion'], $validPhpVersions)) {
            echo "Error: Target PHP version must be one of: " . implode(', ', $validPhpVersions) . "\n";
            exit(1);
        }

        return $config;
    }
}
