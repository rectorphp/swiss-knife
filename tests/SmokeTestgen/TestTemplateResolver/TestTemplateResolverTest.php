<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\TestTemplateResolver;

use Rector\SwissKnife\SmokeTestgen\TestTemplateResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;

/**
 * @see TestTemplateResolver
 */
final class TestTemplateResolverTest extends AbstractTestCase
{
    public function test(): void
    {
        $testTemplateResolver = $this->make(TestTemplateResolver::class);

        $testByPackageSubscribers = $testTemplateResolver->matchProjectPackages(['symfony/dependency-injection']);

        $this->assertCount(1, $testByPackageSubscribers);
    }
}
