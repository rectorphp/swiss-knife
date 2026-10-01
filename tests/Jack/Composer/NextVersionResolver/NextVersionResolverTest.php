<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Jack\Composer\NextVersionResolver;

use Rector\SwissKnife\Jack\Composer\NextVersionResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class NextVersionResolverTest extends AbstractTestCase
{
    private NextVersionResolver $nextVersionResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nextVersionResolver = $this->make(NextVersionResolver::class);
    }

    /**
     * @dataProvider provideData
     */
    public function test(string $packageName, string $composerVersion, string $expectedVersion): void
    {
        $nextVersion = $this->nextVersionResolver->resolve($packageName, $composerVersion);
        $this->assertSame($expectedVersion, $nextVersion);
    }

    /**
     * @return iterable<string[]>
     */
    public static function provideData(): iterable
    {
        yield ['some/package', '^1.0', '^2.0'];
        yield ['some/package', '2.2.*', '2.3.*'];
        yield ['some/package', '4.*', '5.0.*'];

        // special cases
        yield ['symfony/console', '^5.4', '6.0.*'];
    }
}
