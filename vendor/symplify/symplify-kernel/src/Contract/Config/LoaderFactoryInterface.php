<?php

declare (strict_types=1);
namespace EasyCI20220604\Symplify\SymplifyKernel\Contract\Config;

use EasyCI20220604\Symfony\Component\Config\Loader\LoaderInterface;
use EasyCI20220604\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\EasyCI20220604\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220604\Symfony\Component\Config\Loader\LoaderInterface;
}
