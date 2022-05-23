<?php

declare (strict_types=1);
namespace EasyCI20220523\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220523\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220523\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220523\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220523\Symfony\Component\Config\Loader\LoaderInterface;
}
