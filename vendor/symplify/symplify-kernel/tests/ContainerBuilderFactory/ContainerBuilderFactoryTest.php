<?php

declare (strict_types=1);
namespace EasyCI20220605\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use EasyCI20220605\PHPUnit\Framework\TestCase;
use EasyCI20220605\Symplify\SmartFileSystem\SmartFileSystem;
use EasyCI20220605\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use EasyCI20220605\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends \EasyCI20220605\PHPUnit\Framework\TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new \EasyCI20220605\Symplify\SymplifyKernel\ContainerBuilderFactory(new \EasyCI20220605\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(\EasyCI20220605\Symplify\SmartFileSystem\SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
