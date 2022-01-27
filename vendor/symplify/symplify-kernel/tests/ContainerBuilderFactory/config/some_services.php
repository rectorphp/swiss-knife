<?php

declare (strict_types=1);
namespace EasyCI20220127;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220127\Symplify\SmartFileSystem\SmartFileSystem;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\EasyCI20220127\Symplify\SmartFileSystem\SmartFileSystem::class);
};
