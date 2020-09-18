<?php

declare(strict_types=1);

namespace Migrify\EasyCI\HttpKernel;

use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;

final class EasyCIKernel extends AbstractMigrifyKernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
