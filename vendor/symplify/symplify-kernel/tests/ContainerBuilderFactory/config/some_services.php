<?php

declare (strict_types=1);
namespace EasyCI20220607;

use EasyCI20220607\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220607\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
