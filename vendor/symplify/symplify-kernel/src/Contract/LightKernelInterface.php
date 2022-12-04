<?php

declare (strict_types=1);
namespace EasyCI202212\Symplify\SymplifyKernel\Contract;

use EasyCI202212\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface;
    public function getContainer() : ContainerInterface;
}
