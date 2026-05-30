<?php

declare (strict_types=1);
namespace Rector\SwissKnife\SmokeTestgen;

use Rector\SwissKnife\SmokeTestgen\Contract\TestByPackageSubscriberInterface;
use Rector\SwissKnife\SmokeTestgen\TestByPackageSubscriber\ServiceContainerTestByPackageSubscriber;
use SwissKnife202605\Webmozart\Assert\Assert;
final class TestTemplateResolver
{
    /**
     * @var TestByPackageSubscriberInterface[]
     */
    private $testByPackageSubscribers;
    public function __construct(ServiceContainerTestByPackageSubscriber $serviceContainerTestByPackageSubscriber)
    {
        $this->testByPackageSubscribers[] = $serviceContainerTestByPackageSubscriber;
    }
    /**
     * @param string[] $requiredPackages
     * @return TestByPackageSubscriberInterface[]
     */
    public function matchProjectPackages(array $requiredPackages) : array
    {
        Assert::allString($requiredPackages);
        // find all subscribers, that match any of the required packages
        return \array_filter($this->testByPackageSubscribers, function (TestByPackageSubscriberInterface $testByPackageSubscriber) use($requiredPackages) : bool {
            return \array_intersect($testByPackageSubscriber->getPackageNames(), $requiredPackages) !== [];
        });
    }
}
