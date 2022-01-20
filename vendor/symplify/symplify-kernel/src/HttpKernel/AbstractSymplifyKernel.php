<?php

declare (strict_types=1);
namespace EasyCI20220120\Symplify\SymplifyKernel\HttpKernel;

use EasyCI20220120\Symfony\Component\DependencyInjection\Container;
use EasyCI20220120\Symfony\Component\DependencyInjection\ContainerInterface;
use EasyCI20220120\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use EasyCI20220120\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use EasyCI20220120\Symplify\SymplifyKernel\ContainerBuilderFactory;
use EasyCI20220120\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use EasyCI20220120\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use EasyCI20220120\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements \EasyCI20220120\Symplify\SymplifyKernel\Contract\LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles) : \EasyCI20220120\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $containerBuilderFactory = new \EasyCI20220120\Symplify\SymplifyKernel\ContainerBuilderFactory(new \EasyCI20220120\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $compilerPasses[] = new \EasyCI20220120\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        $configFiles[] = \EasyCI20220120\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \EasyCI20220120\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Container) {
            throw new \EasyCI20220120\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
}
