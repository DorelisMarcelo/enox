<?php

$paths = [
    '/var/www/imageretriever/Classes'
];

$autoloadPath = '/var/www/nn_hn/mobDependencies/autoload.php';

$analyzeCommand = 'php enox.php --do=analyze --path=%path% --autoload-path=%autoloadPath% --analysis-name=%analysis_name% --target-php=8.2 --phpstan-level=0';
$upgradeCommand = 'php enox.php --do=upgrade --path=%path% --autoload-path=%autoloadPath%';


$useCommand = $upgradeCommand;
$commands = [];
foreach ($paths as $path) {
    $pathExploded = explode('/', $path);
    $analysisName = str_replace('.php', '', end($pathExploded));

    $commands[] = str_replace([
        '%path%',
        '%analysis_name%',
        '%autoloadPath%'
    ], [
        $path,
        $analysisName,
        $autoloadPath
    ], $useCommand);
}

echo implode(' && ', $commands);
