<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\DependencyInjection\ContainerFactory;

abstract class AbstractTestCase extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $this->container = $containerFactory->create();
    }

    /**
     * @template TType of object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        return $this->container->make($type);
    }
}
