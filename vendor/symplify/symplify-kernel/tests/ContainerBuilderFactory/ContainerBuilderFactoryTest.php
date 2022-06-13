<?php

declare (strict_types=1);
namespace EasyCI202206\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use EasyCI202206\PHPUnit\Framework\TestCase;
use EasyCI202206\Symplify\SmartFileSystem\SmartFileSystem;
use EasyCI202206\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use EasyCI202206\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
