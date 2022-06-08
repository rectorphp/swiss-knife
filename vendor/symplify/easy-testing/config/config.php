<?php

declare (strict_types=1);
namespace EasyCI20220608;

use EasyCI20220608\Symfony\Component\Console\Application;
use EasyCI20220608\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220608\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function EasyCI20220608\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('EasyCI20220608\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(Application::class)->call('add', [service(ValidateFixtureSkipNamingCommand::class)]);
};
