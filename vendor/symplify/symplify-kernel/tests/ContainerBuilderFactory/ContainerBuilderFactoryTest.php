<?php

declare (strict_types=1);
namespace EasyCI20220416\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use EasyCI20220416\PHPUnit\Framework\TestCase;
use EasyCI20220416\Symplify\SmartFileSystem\SmartFileSystem;
use EasyCI20220416\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use EasyCI20220416\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends \EasyCI20220416\PHPUnit\Framework\TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new \EasyCI20220416\Symplify\SymplifyKernel\ContainerBuilderFactory(new \EasyCI20220416\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $container = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $container->has(\EasyCI20220416\Symplify\SmartFileSystem\SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
