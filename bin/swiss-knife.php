<?php

declare (strict_types=1);
namespace SwissKnife202506;

use SwissKnife202506\Symfony\Component\Console\Application;
use SwissKnife202506\Symfony\Component\Console\Input\ArgvInput;
use SwissKnife202506\Symfony\Component\Console\Output\ConsoleOutput;
use Rector\SwissKnife\DependencyInjection\ContainerFactory;
$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}
// load scoped autoload just once, order matters
$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
$containerFactory = new ContainerFactory();
$container = $containerFactory->create();
$application = $container->make(Application::class);
$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
