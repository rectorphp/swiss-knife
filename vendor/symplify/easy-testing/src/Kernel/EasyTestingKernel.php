<?php

declare (strict_types=1);
namespace EasyCI20220117\Symplify\EasyTesting\Kernel;

use EasyCI20220117\Psr\Container\ContainerInterface;
use EasyCI20220117\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI20220117\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \EasyCI20220117\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \EasyCI20220117\Psr\Container\ContainerInterface
    {
        $configFiles[] = \EasyCI20220117\Symplify\EasyTesting\ValueObject\EasyTestingConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
