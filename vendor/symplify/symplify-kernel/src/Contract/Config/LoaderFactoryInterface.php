<?php

declare (strict_types=1);
namespace EasyCI20220514\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220514\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220514\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220514\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220514\Symfony\Component\Config\Loader\LoaderInterface;
}
