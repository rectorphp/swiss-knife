<?php

declare (strict_types=1);
namespace EasyCI202211\Symplify\SymplifyKernel\Contract\Config;

use EasyCI202211\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI202211\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
