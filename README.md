# Enox - PHP Codebase Upgrade Tool

A powerful PHP upgrade toolset that helps you migrate your codebase from PHP 7.2 to PHP 8.2 without losing your hair. Built with Rector, PHPStan, and PHPCompatibility for comprehensive analysis and safe upgrades.

This project does not upgrade your dependencies, only your codebase.
Dependency solving is out of scope for this project.

## Features

- ✅ **Safe PHP 7.2 → 8.2 upgrade** with comprehensive Rector rule sets
- ✅ **Static analysis** with PHPStan for code quality checks
- ✅ **Compatibility analysis** with PHPCompatibility for version compatibility
- ✅ **Dry-run mode** to preview changes before applying them
- ✅ **Detailed reports** with timestamped analysis results
- ✅ **Configurable options** for memory, parallelism, and file extensions
- ✅ **Supports legacy files** (.inc) and modern PHP files (.php)

## Installation

1. Clone the repository:
2. Install dependencies:
composer install

## Usage

### Basic Commands

```bash
# Show help
php enox.php --help

# Analyze PHP compatibility (recommended first)
php enox.php --do=analyze --path=/path/to/your/project

# Preview upgrade changes (dry-run)
php enox.php --do=upgrade --path=/path/to/project --dry-run --autoload-path=/path/to/vendor/autoload.php

# Apply upgrade
php enox.php --do=upgrade --path=/path/to/project --autoload-path=/path/to/vendor/autoload.php
```

### Actions

#### `upgrade`
Upgrade your PHP codebase from 7.2 to 8.2 using Rector with extensive rule sets and optimizations.

**Required Options:**
- `--path, -p <directory>` - Path to the codebase to upgrade
- `--autoload-path, -a <file>` - Path to vendor/autoload.php (required for trait and class resolution)

**Optional Options:**
- `--dry-run, -d` - Show changes without modifying files (recommended first)
- `--clear-cache, -c` - Clear Rector cache before processing
- `--no-progress` - Disable progress bar
- `--parallel, -P <number>` - Number of parallel processes (default: 16)
- `--memory-limit, -m <limit>` - Memory limit for Rector (default: 8G)
- `--extensions, -e <list>` - File extensions to process (default: php,inc)

#### `analyze`
Analyze PHP codebase compatibility and quality using PHPStan and PHPCompatibility.

**Required Options:**
- `--path, -p <directory>` - Path to the codebase to analyze

**Optional Options:**
- `--root-path, -r <directory>` - Root project path for autoloader (default: same as path)
- `--phpstan-level, -l <level>` - PHPStan analysis level (0-9, default: 5)
- `--target-php, -t <version>` - Target PHP version (7.2-8.3, default: 8.2)
- `--memory-limit, -m <limit>` - Memory limit for analysis (default: 8G)
- `--output-format, -f <format>` - Output format (json, text, default: json)
- `--analysis-name, -n <name>` - Custom analysis name (default: timestamp)

## Analysis Reports

Analysis reports are automatically saved to the `analysisCompatibilityResult/` directory with timestamped subdirectories. Each analysis creates:

- **PHPStan report** - Static analysis results and code quality issues
- **PHPCompatibility report** - Version compatibility issues
- **Summary report** - Consolidated overview of all findings

### Report Structure
```
analysisCompatibilityResult/
└── [timestamp]-[analysis-name]/
    ├── phpstan-report.json
    ├── phpcompatibility-report.json
    └── summary.json
```

## Examples

### Analyze a Project
```bash
# Basic analysis
php enox.php --do=analyze --path=/path/to/project

# Analyze with custom PHPStan level
php enox.php --do=analyze --path=/path/to/project --phpstan-level=7

# Analyze for PHP 8.1 compatibility
php enox.php --do=analyze --path=/path/to/project --target-php=8.1

# Analyze with custom name
php enox.php --do=analyze --path=/path/to/project --analysis-name=my-project-analysis
```

### Upgrade a Project
```bash
# Preview changes (recommended first)
php enox.php --do=upgrade --path=/path/to/project --dry-run --autoload-path=/path/to/vendor/autoload.php

# Apply upgrade
php enox.php --do=upgrade --path=/path/to/project --autoload-path=/path/to/vendor/autoload.php

# Upgrade with custom memory limit
php enox.php --do=upgrade --path=/path/to/project --autoload-path=/path/to/vendor/autoload.php --memory-limit=4G
```

## Development Tools

This project uses several development tools for code quality:

- **Rector** - Automated PHP refactoring and upgrades
- **PHPStan** - Static analysis for code quality
- **PHPCompatibility** - PHP version compatibility checking
- **PHP_CodeSniffer** - Code style and standards checking

## Requirements

- PHP 7.2 or higher. Recommended PHP 8.2 or higher for best compatibility.
- Composer
- Sufficient memory (recommended 8GB for large projects)
