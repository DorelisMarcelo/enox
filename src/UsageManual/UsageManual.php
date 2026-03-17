<?php

namespace Enox\UsageManual;

class UsageManual
{
    private const USAGE_ROWS = [
        "Enox - PHP Codebase Upgrade Tool",
        "===============================\n",
        "Usage: php enox.php --do=<action> [options]",
        "       php enox.php --help\n",
        "Actions:",
        "  upgrade    Run the PHP codebase upgrade from 7.2 to 8.2",
        "  analyze    Analyze PHP codebase compatibility and quality\n",
        "Help Flags:",
        "  --help, -h            Show this help message and exit\n",
        "Upgrade Options:",
        "  --path, -p <directory>    Path to the codebase to upgrade (required)",
        "  --dry-run, -d             Show changes without modifying files",
        "  --clear-cache, -c         Clear Rector cache before processing",
        "  --no-progress             Disable progress bar\n",
        "Analyze Options:",
        "  --path, -p <directory>           Path to the codebase to analyze (required)",
        "  --root-path, -r <directory>      Root project path for autoloader (default: same as path)",
        "  --phpstan-level, -l <level>      PHPStan analysis level (0-9, default: 5)",
        "  --target-php, -t <version>       Target PHP version (7.2-8.3, default: 8.2)",
        "  --memory-limit, -m <limit>       Memory limit for analysis (default: 8G)",
        "  --output-format, -f <format>     Output format (json, default: json)",
        "  --analysis-name, -n <name>       Custom analysis name (default: timestamp)\n",
        "Examples:",
        "  # Upgrade preview (recommended first)",
        "  php enox.php --do=upgrade --path /path/to/project --dry-run",
        "  # Apply upgrade",
        "  php enox.php --do=upgrade --path /path/to/project",
        "  # Analyze compatibility",
        "  php enox.php --do=analyze --path /path/to/project",
        "  # Analyze with custom PHPStan level",
        "  php enox.php --do=analyze --path /path/to/project --phpstan-level=7",
        "  # Analyze for PHP 8.1 compatibility",
        "  php enox.php --do=analyze --path /path/to/project --target-php=8.1",
        "  # Analyze subdirectory with custom root path",
        "  php enox.php --do=analyze --path=/path/to/analyze --autoload-path=/path/to/vendor/autoloader.php",
        "  # Analyze with custom name",
        "  php enox.php --do=analyze --path /path/to/project --analysis-name=my-project-analysis",
        "  # Show help",
        "  php enox.php --help",
        "  php enox.php -h\n",
        "Features:",
        "  ✅ PHP 7.2 → 8.2 upgrade",
        "  ✅ 200+ Rector rules",
        "  ✅ Code quality improvements",
        "  ✅ Dead code removal",
        "  ✅ Type declarations",
        "  ✅ Modern PHP syntax",
        "  ✅ Framework-specific rules",
        "  ✅ PHPStan static analysis",
        "  ✅ PHPCompatibility checks",
        "  ✅ Detailed analysis reports",
        "  ✅ Timestamped report storage"
    ];

    public static function show(): string
    {
        return implode("\n", self::USAGE_ROWS) . "\n\n";
    }
}
