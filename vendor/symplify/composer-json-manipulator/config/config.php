<?php

declare (strict_types=1);
namespace EasyCI20220605;

use EasyCI20220605\Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220605\Symplify\ComposerJsonManipulator\ValueObject\Option;
use EasyCI20220605\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use EasyCI20220605\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI20220605\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use EasyCI20220605\Symplify\SmartFileSystem\SmartFileSystem;
use function EasyCI20220605\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\EasyCI20220605\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('EasyCI20220605\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(\EasyCI20220605\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\EasyCI20220605\Symplify\PackageBuilder\Reflection\PrivatesCaller::class);
    $services->set(\EasyCI20220605\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\EasyCI20220605\Symfony\Component\DependencyInjection\Loader\Configurator\service('service_container')]);
    $services->set(\EasyCI20220605\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\EasyCI20220605\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\EasyCI20220605\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220605\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
};
