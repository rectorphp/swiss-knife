<?php

declare (strict_types=1);
namespace EasyCI202301\Symplify\SymplifyKernel\Contract\Config;

use EasyCI202301\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI202301\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
