<?php

declare (strict_types=1);
namespace EasyCI20220516;

use EasyCI20220516\Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220516\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use EasyCI20220516\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI20220516\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use EasyCI20220516\Symplify\SmartFileSystem\FileSystemFilter;
use EasyCI20220516\Symplify\SmartFileSystem\FileSystemGuard;
use EasyCI20220516\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use EasyCI20220516\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI20220516\Symplify\SmartFileSystem\SmartFileSystem;
use function EasyCI20220516\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    // symfony style
    $services->set(\EasyCI20220516\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\EasyCI20220516\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\EasyCI20220516\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220516\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(\EasyCI20220516\Symplify\SmartFileSystem\Finder\FinderSanitizer::class);
    $services->set(\EasyCI20220516\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\EasyCI20220516\Symplify\SmartFileSystem\Finder\SmartFinder::class);
    $services->set(\EasyCI20220516\Symplify\SmartFileSystem\FileSystemGuard::class);
    $services->set(\EasyCI20220516\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->set(\EasyCI20220516\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\EasyCI20220516\Symfony\Component\DependencyInjection\Loader\Configurator\service('service_container')]);
    $services->set(\EasyCI20220516\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
