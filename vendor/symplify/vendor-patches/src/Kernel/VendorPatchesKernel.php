<?php

declare (strict_types=1);
namespace EasyCI20220527\Symplify\VendorPatches\Kernel;

use EasyCI20220527\Psr\Container\ContainerInterface;
use EasyCI20220527\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use EasyCI20220527\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class VendorPatchesKernel extends \EasyCI20220527\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \EasyCI20220527\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = \EasyCI20220527\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
