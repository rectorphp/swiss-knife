<?php

declare (strict_types=1);
namespace EasyCI202207;

use EasyCI202207\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202207\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
