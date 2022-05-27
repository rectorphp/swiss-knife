<?php

declare (strict_types=1);
namespace EasyCI20220527;

use EasyCI20220527\SebastianBergmann\Diff\Differ;
use EasyCI20220527\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use EasyCI20220527\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220527\Symplify\PackageBuilder\Composer\VendorDirProvider;
use EasyCI20220527\Symplify\PackageBuilder\Yaml\ParametersMerger;
use EasyCI20220527\Symplify\SmartFileSystem\Json\JsonFileSystem;
use EasyCI20220527\Symplify\VendorPatches\Console\VendorPatchesApplication;
use function EasyCI20220527\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('EasyCI20220527\Symplify\VendorPatches\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    $services->set(\EasyCI20220527\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder::class)->args(['$addLineNumbers' => \true]);
    $services->set(\EasyCI20220527\SebastianBergmann\Diff\Differ::class)->args(['$outputBuilder' => \EasyCI20220527\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220527\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder::class)]);
    $services->set(\EasyCI20220527\Symplify\PackageBuilder\Composer\VendorDirProvider::class);
    $services->set(\EasyCI20220527\Symplify\SmartFileSystem\Json\JsonFileSystem::class);
    // for autowired commands
    $services->alias(\EasyCI20220527\Symfony\Component\Console\Application::class, \EasyCI20220527\Symplify\VendorPatches\Console\VendorPatchesApplication::class);
    $services->set(\EasyCI20220527\Symplify\PackageBuilder\Yaml\ParametersMerger::class);
};
