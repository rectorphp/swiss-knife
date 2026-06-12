<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'secret' => 'test',
    ]);
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => 'sqlite:///:memory:',
        ],
    ]);
};
