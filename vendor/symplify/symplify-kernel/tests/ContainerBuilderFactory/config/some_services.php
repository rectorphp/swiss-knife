<?php

declare (strict_types=1);
namespace EasyCI202208;

use EasyCI202208\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202208\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
