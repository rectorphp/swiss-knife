<?php

declare (strict_types=1);
namespace EasyCI20220126\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220126\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220126\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220126\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220126\Symfony\Component\Config\Loader\LoaderInterface;
}
