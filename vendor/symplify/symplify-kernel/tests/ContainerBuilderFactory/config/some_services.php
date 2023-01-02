<?php

declare (strict_types=1);
namespace EasyCI202301;

use EasyCI202301\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
