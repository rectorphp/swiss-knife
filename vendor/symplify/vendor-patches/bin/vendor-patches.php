<?php

declare (strict_types=1);
namespace EasyCI20220527;

use EasyCI20220527\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
use EasyCI20220527\Symplify\VendorPatches\Kernel\VendorPatchesKernel;
$possibleAutoloadPaths = [__DIR__ . '/../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php', __DIR__ . '/../../../vendor/autoload.php'];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (!\file_exists($possibleAutoloadPath)) {
        continue;
    }
    require_once $possibleAutoloadPath;
}
$kernelBootAndApplicationRun = new \EasyCI20220527\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\EasyCI20220527\Symplify\VendorPatches\Kernel\VendorPatchesKernel::class);
$kernelBootAndApplicationRun->run();
