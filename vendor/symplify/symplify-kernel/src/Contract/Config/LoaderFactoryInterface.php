<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\SymplifyKernel\Contract\Config;

use EasyCI202208\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI202208\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
