<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\TestByPackageSubscriber;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\SmokeTestgen\TestByPackageSubscriber\ServiceContainerTestByPackageSubscriber;

final class ServiceContainerTestByPackageSubscriberTest extends TestCase
{
    public function test(): void
    {
        $subscriber = new ServiceContainerTestByPackageSubscriber();

        $this->assertSame(
            ['symfony/symfony', 'symfony/dependency-injection'],
            $subscriber->getPackageNames()
        );
        $this->assertFileExists($subscriber->getTemplateFilePath());
    }
}
