<?php

declare(strict_types=1);

namespace __SMOKE_TEST_NAMESPACE__;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractContainerTestCase extends TestCase
{
    protected static Container $container;

    protected static KernelInterface $kernel;

    protected function setUp(): void
    {
        // @todo configure your test environment: most likely "tests", "ci" or "dev"
        $kernel = new \__KERNEL_CLASS_PLACEHOLDER__('tests', true);
        $kernel->boot();

        self::$kernel = $kernel;
        self::$container = $kernel->getContainer()->get('test.service_container');
    }

    /**
     * @template TService as object
     *
     * @param class-string<TService>|string $type
     * @return ($type is "event_dispatcher" ? EventDispatcherInterface :
     *  ($type is "router" ? RouterInterface :
     *  ($type is "debug.controller_resolver" ? ControllerResolverInterface :
     *  ($type is "controller_resolver" ? ControllerResolverInterface : TService)
     * )))
     */
    protected function getService(string $type): object
    {
        /** @var Container $container */
        $container = self::$container;

        return $container->get($type);
    }
}
