<?php

declare (strict_types=1);
namespace EasyCI20220501;

use EasyCI20220501\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220501\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function EasyCI20220501\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('EasyCI20220501\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\EasyCI20220501\Symfony\Component\Console\Application::class)->call('add', [\EasyCI20220501\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220501\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand::class)]);
};
