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
        "  --path, -p <directory>        Path to the codebase to upgrade (required)",
        "  --dry-run, -d                 Show changes without modifying files (recommended first)",
        "  --clear-cache, -c             Clear Rector cache before processing",
        "  --no-progress                 Disable progress bar",
        "  --autoload-path, -a <file>    Path to vendor/autoload.php (required for trait and class resolution)",
        "  --parallel, -P <number>       Number of parallel processes (default: 16)",
        "  --memory-limit, -m <limit>    Memory limit for Rector (default: 8G)",
        "  --extensions, -e <list>       File extensions to process (default: php,inc)\n",
        "Analyze Options:",
        "  --path, -p <directory>           Path to the codebase to analyze (required)",
        "  --root-path, -r <directory>      Root project path for autoloader (default: same as path)",
        "  --phpstan-level, -l <level>      PHPStan analysis level (0-9, default: 5)",
        "  --target-php, -t <version>       Target PHP version (7.2-8.3, default: 8.2)",
        "  --memory-limit, -m <limit>       Memory limit for analysis (default: 8G)",
        "  --output-format, -f <format>     Output format (json, text, default: json)",
        "  --analysis-name, -n <name>       Custom analysis name (default: timestamp)\n",
        "Analysis Reports:",
        "  📁 Reports are saved to: analysisCompatibilityResult/[timestamp]-[name]/",
        "  📊 Includes: PHPStan analysis, PHPCompatibility check, and summary",
        "  📈 Each analysis creates timestamped subdirectories with detailed reports\n",
        "Examples:",
        "  # Upgrade preview (dry-run, recommended first)",
        "  php enox.php --do=upgrade --path=/path/to/project --dry-run --autoload-path=/path/to/vendor/autoload.php",
        "  # Apply upgrade",
        "  php enox.php --do=upgrade --path=/path/to/project --autoload-path=/path/to/vendor/autoload.php",
        "  # Analyze PHP compatibility",
        "  php enox.php --do=analyze --path=/path/to/project",
        "  # Analyze with custom PHPStan level",
        "  php enox.php --do=analyze --path=/path/to/project --phpstan-level=7",
        "  # Analyze for PHP 8.1 compatibility",
        "  php enox.php --do=analyze --path=/path/to/project --target-php=8.1",
        "  # Analyze subdirectory with custom root path",
        "  php enox.php --do=analyze --path=/path/to/analyze --autoload-path=/path/to/vendor/autoload.php",
        "  # Analyze with custom name",
        "  php enox.php --do=analyze --path=/path/to/project --analysis-name=my-project-analysis",
        "  # Show help",
        "  php enox.php --help",
        "  php enox.php -h\n",
        "Features:",
        "  ✅ Safe PHP 7.2 → 8.2 upgrade with Rector sets PHP_73 → PHP_82",
        "  ✅ Includes legacy .inc files and modern .php files",
        "  ✅ Optional dry-run to preview changes",
        "  ✅ Handles traits and autoloaded classes correctly",
        "  ✅ 200+ Rector rules for migration and modern syntax",
        "  ✅ Code quality improvements (optional post-migration)",
        "  ✅ Dead code removal (optional post-migration)",
        "  ✅ Type declarations and stricter typing support",
        "  ✅ Static analysis with PHPStan",
        "  ✅ Compatibility checks for PHP 7.x → 8.2",
        "  ✅ Timestamped detailed analysis reports",
        "  ✅ Configurable memory, parallelism, and file extensions",
        "  ✅ Safe incremental upgrade strategy recommended"
    ];

    public static function show(): string
    {
        return implode("\n", self::USAGE_ROWS) . "\n\n";
    }
}
