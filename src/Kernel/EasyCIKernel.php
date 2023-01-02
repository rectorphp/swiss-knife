<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Kernel;

use EasyCI202301\Psr\Container\ContainerInterface;
use EasyCI202301\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCIKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        return $this->create($configFiles);
    }
}
