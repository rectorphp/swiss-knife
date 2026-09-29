<?php

declare(strict_types=1);

use Entropy\Console\ConsoleApplication;
use Rector\SwissKnife\DependencyInjection\ContainerFactory;

// load autoload just once, order matters
$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}

// keep in case the tool is built for a lower PHP version, where the bundled
// nikic/php-parser references newer token constants directly - define them to avoid fatal errors
// (see https://github.com/easy-coding-standard/ecs/blob/main/bin/ecs.php for the same approach)
if (! defined('T_FN')) {
    define('T_FN', 5025);
}

if (! defined('T_COALESCE_EQUAL')) {
    define('T_COALESCE_EQUAL', 5030);
}

if (! defined('T_BAD_CHARACTER')) {
    define('T_BAD_CHARACTER', 5035);
}

$containerFactory = new ContainerFactory();
$container = $containerFactory->create();

$consoleApplication = $container->make(ConsoleApplication::class);

$exitCode = $consoleApplication->run($argv);
exit($exitCode);
