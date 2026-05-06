<?php

$paths = [
    '/var/www/imageretriever/Classes'
];

$analyzeCommand = 'php enox.php --do=analyze --path=%path% --autoload-path=/var/www/imageretriever/vendor/autoload.php --analysis-name=%analysis_name% --target-php=8.2 --phpstan-level=0';
$upgradeCommand = 'php enox.php --do=upgrade --path=%path% --autoload-path=/var/www/imageretriever/vendor/autoload.php';

$commands = [];
foreach ($paths as $path) {
    $pathExploded = explode('/', $path);
    $analysisName = str_replace('.php', '', end($pathExploded));

    $commands[] = str_replace('%path%', $path, $upgradeCommand);
}

echo implode(' && ', $commands);
