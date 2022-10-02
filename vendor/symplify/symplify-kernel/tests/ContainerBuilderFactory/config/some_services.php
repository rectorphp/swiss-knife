<?php

declare (strict_types=1);
namespace EasyCI202210;

use EasyCI202210\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202210\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
