<?php

declare (strict_types=1);
namespace EasyCI202212;

use EasyCI202212\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202212\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
