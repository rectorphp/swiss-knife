<?php

declare(strict_types=1);

namespace Rector\SwissKnife\SmokeTestgen\TestByPackageSubscriber;

use Rector\SwissKnife\SmokeTestgen\Contract\TestByPackageSubscriberInterface;

final class ServiceContainerTestByPackageSubscriber implements TestByPackageSubscriberInterface
{
    /**
     * @return string[]
     */
    public function getPackageNames(): array
    {
        return ['symfony/symfony', 'symfony/dependency-injection'];
    }

    public function getTemplateFilePath(): string
    {
        return __DIR__ . '/../../../templates/SmokeTests/Symfony/ServiceContainerTest.php';
    }
}
