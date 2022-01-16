<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\SymplifyKernel;

use EasyCI20220116\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder;
use EasyCI20220116\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use EasyCI20220116\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
use EasyCI20220116\Symplify\SymplifyKernel\DependencyInjection\LoadExtensionConfigsCompilerPass;
use EasyCI20220116\Webmozart\Assert\Assert;
final class ContainerBuilderFactory
{
    /**
     * @var \Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
     */
    private $loaderFactory;
    public function __construct(\EasyCI20220116\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface $loaderFactory)
    {
        $this->loaderFactory = $loaderFactory;
    }
    /**
     * @param ExtensionInterface[] $extensions
     * @param CompilerPassInterface[] $compilerPasses
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles) : \EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder
    {
        \EasyCI20220116\Webmozart\Assert\Assert::allString($configFiles);
        \EasyCI20220116\Webmozart\Assert\Assert::allFile($configFiles);
        $containerBuilder = new \EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder();
        $this->registerExtensions($containerBuilder, $extensions);
        $this->registerConfigFiles($containerBuilder, $configFiles);
        $this->registerCompilerPasses($containerBuilder, $compilerPasses);
        // this calls load() method in every extensions
        // ensure these extensions are implicitly loaded
        $compilerPassConfig = $containerBuilder->getCompilerPassConfig();
        $compilerPassConfig->setMergePass(new \EasyCI20220116\Symplify\SymplifyKernel\DependencyInjection\LoadExtensionConfigsCompilerPass());
        return $containerBuilder;
    }
    /**
     * @param ExtensionInterface[] $extensions
     */
    private function registerExtensions(\EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $extensions) : void
    {
        foreach ($extensions as $extension) {
            $containerBuilder->registerExtension($extension);
        }
    }
    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    private function registerCompilerPasses(\EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $compilerPasses) : void
    {
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
    }
    /**
     * @param string[] $configFiles
     */
    private function registerConfigFiles(\EasyCI20220116\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $configFiles) : void
    {
        $delegatingLoader = $this->loaderFactory->create($containerBuilder, \getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }
    }
}
