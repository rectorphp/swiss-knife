<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symplify\EasyCI\DependencyInjection\ContainerFactory;

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

$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}

$containerFactory = new ContainerFactory();
$container = $containerFactory->create();

$application = $container->make(Application::class);

$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
