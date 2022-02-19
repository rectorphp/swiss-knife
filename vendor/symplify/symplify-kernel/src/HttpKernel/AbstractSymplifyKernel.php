<?php

declare (strict_types=1);
namespace EasyCI20220219\Symplify\SymplifyKernel\HttpKernel;

use EasyCI20220219\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use EasyCI20220219\Symfony\Component\DependencyInjection\Container;
use EasyCI20220219\Symfony\Component\DependencyInjection\ContainerInterface;
use EasyCI20220219\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use EasyCI20220219\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use EasyCI20220219\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use EasyCI20220219\Symplify\SymplifyKernel\ContainerBuilderFactory;
use EasyCI20220219\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use EasyCI20220219\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use EasyCI20220219\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements \EasyCI20220219\Symplify\SymplifyKernel\Contract\LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []) : \EasyCI20220219\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $containerBuilderFactory = new \EasyCI20220219\Symplify\SymplifyKernel\ContainerBuilderFactory(new \EasyCI20220219\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $compilerPasses[] = new \EasyCI20220219\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        $configFiles[] = \EasyCI20220219\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \EasyCI20220219\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \EasyCI20220219\Symfony\Component\DependencyInjection\Container) {
            throw new \EasyCI20220219\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
}
