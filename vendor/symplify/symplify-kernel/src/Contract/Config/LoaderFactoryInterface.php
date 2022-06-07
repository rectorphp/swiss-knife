<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220607\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220607\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
