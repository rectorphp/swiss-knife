<?php

declare (strict_types=1);
namespace EasyCI20220429\Symplify\SymplifyKernel\Contract;

use EasyCI20220429\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \EasyCI20220429\Psr\Container\ContainerInterface;
    public function getContainer() : \EasyCI20220429\Psr\Container\ContainerInterface;
}
