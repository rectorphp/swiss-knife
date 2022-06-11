<?php

declare (strict_types=1);
namespace EasyCI20220611\Symplify\EasyTesting\Kernel;

use EasyCI20220611\Psr\Container\ContainerInterface;
use EasyCI20220611\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI20220611\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
