<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Enum;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Enum\SymfonyClass;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SymfonyClassTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertSame(ContainerConfigurator::class, SymfonyClass::CONTAINER_CONFIGURATOR_CLASS);
    }
}
