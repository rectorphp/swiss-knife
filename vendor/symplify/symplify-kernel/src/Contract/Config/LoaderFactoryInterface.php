<?php

declare (strict_types=1);
namespace EasyCI20220219\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220219\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220219\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220219\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220219\Symfony\Component\Config\Loader\LoaderInterface;
}
