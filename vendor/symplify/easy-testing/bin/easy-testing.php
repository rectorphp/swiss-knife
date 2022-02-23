<?php

declare (strict_types=1);
namespace EasyCI20220223;

use EasyCI20220223\Symplify\EasyTesting\Kernel\EasyTestingKernel;
use EasyCI20220223\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
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
$kernelBootAndApplicationRun = new \EasyCI20220223\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\EasyCI20220223\Symplify\EasyTesting\Kernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
