<?php

declare (strict_types=1);
namespace EasyCI20220527\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220527\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220527\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220527\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220527\Symfony\Component\Config\Loader\LoaderInterface;
}
