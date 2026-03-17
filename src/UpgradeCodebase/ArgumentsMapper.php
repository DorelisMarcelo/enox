<?php

namespace Enox\UpgradeCodebase;

class ArgumentsMapper
{
    /**
     * Parse command line arguments and return configuration
     */
    public static function map(): array
    {
            global $argv;

            $config = [
                'path' => getenv('RECTOR_PATH') ?: '',
                'dryRun' => false,
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
                    case '--dry-run':
                    case '-d':
                        $config['dryRun'] = (int)$value == 1;
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

            return $config;
    }
}
