<?php

declare (strict_types=1);
namespace EasyCI20220325\Symplify\EasyTesting\Kernel;

use EasyCI20220325\Psr\Container\ContainerInterface;
use EasyCI20220325\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI20220325\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \EasyCI20220325\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \EasyCI20220325\Psr\Container\ContainerInterface
    {
        $configFiles[] = \EasyCI20220325\Symplify\EasyTesting\ValueObject\EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
