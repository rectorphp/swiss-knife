<?php

declare (strict_types=1);
namespace EasyCI20220610\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220610\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220610\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
