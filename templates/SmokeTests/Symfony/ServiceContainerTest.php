<?php

declare(strict_types=1);

namespace __SMOKE_TEST_NAMESPACE__;

use Throwable;

final class ServiceContainerTest extends AbstractContainerTestCase
{
    /**
     * Test for configs, that are build correctly including autowiring, services count etc.
     */
    public function testServiceConstruction(): void
    {
        $serviceIds = self::$container->getServiceIds();

        $checkedServiceCount = 0;

        foreach ($serviceIds as $serviceId) {
            if ($this->isDynamicService($serviceId)) {
                continue;
            }

            // trigger service creation
            try {
                self::$container->get($serviceId);
            } catch (Throwable $throwable) {
                $this->fail(sprintf('Service "%s" could not be created because:%s%s', $serviceId, PHP_EOL, $throwable->getMessage()));
            }

            ++$checkedServiceCount;
        }

        // @todo update this number to match your service count
        $this->assertSame(100000, $checkedServiceCount);
    }

    /**
     * Check if the service is dynamic, that would trigger non-unit test behavior.
     * E.g. database trigger, external service call etc.
     */
    private function isDynamicService(string $serviceId): bool
    {
        // the "session.storage.*" services invoke session, that crashes due to session_start()
        if (str_contains($serviceId, 'session')) {
            return true;
        }

        if (str_contains($serviceId, 'routing.loader.service')) {
            return true;
        }

        if (str_starts_with($serviceId, 'doctrine.')) {
            return true;
        }

        if (str_contains($serviceId, 'cache_connection')) {
            return true;
        }

        return in_array(
            $serviceId,
            ['kernel', 'database_connection', 'event_dispatcher', 'debug.controller_resolver'],
            true
        );
    }
}
