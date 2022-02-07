<?php

declare (strict_types=1);
namespace EasyCI20220207\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220207\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220207\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220207\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220207\Symfony\Component\Config\Loader\LoaderInterface;
}
