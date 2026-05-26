<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\Utils;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\SmokeTestgen\TestByPackageSubscriber\ServiceContainerTestByPackageSubscriber;
use Rector\SwissKnife\SmokeTestgen\Utils\TestPathResolver;

/**
 * @see \Rector\SwissKnife\SmokeTestgen\Utils\TestPathResolver
 */
final class TestPathResolverTest extends TestCase
{
    public function test(): void
    {
        $serviceContainerTestByPackageSubscriber = new ServiceContainerTestByPackageSubscriber();
        $testPath = TestPathResolver::resolve($serviceContainerTestByPackageSubscriber, 'some-path');

        $this->assertSame('some-path/ServiceContainerTest.php', $testPath);
    }
}
