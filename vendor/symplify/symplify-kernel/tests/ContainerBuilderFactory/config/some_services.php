<?php

declare (strict_types=1);
namespace EasyCI202209;

use EasyCI202209\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202209\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
