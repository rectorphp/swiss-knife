<?php

declare (strict_types=1);
namespace EasyCI202207\Symplify\EasyTesting\Kernel;

use EasyCI202207\Psr\Container\ContainerInterface;
use EasyCI202207\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI202207\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
