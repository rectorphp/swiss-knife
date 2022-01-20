<?php

declare (strict_types=1);
namespace EasyCI20220120\Symplify\SymplifyKernel\Config\Loader;

use EasyCI20220120\Symfony\Component\Config\FileLocator;
use EasyCI20220120\Symfony\Component\Config\Loader\DelegatingLoader;
use EasyCI20220120\Symfony\Component\Config\Loader\GlobFileLoader;
use EasyCI20220120\Symfony\Component\Config\Loader\LoaderResolver;
use EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder;
use EasyCI20220120\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use EasyCI20220120\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements \EasyCI20220120\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
{
    public function create(\EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI20220120\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new \EasyCI20220120\Symfony\Component\Config\FileLocator([$currentWorkingDirectory]);
        $loaders = [new \EasyCI20220120\Symfony\Component\Config\Loader\GlobFileLoader($fileLocator), new \EasyCI20220120\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \EasyCI20220120\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \EasyCI20220120\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
