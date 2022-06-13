<?php

declare (strict_types=1);
namespace EasyCI202206;

use EasyCI202206\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202206\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
