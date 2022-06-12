<?php

declare (strict_types=1);
namespace EasyCI20220612\Symplify\EasyTesting\Kernel;

use EasyCI20220612\Psr\Container\ContainerInterface;
use EasyCI20220612\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI20220612\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
