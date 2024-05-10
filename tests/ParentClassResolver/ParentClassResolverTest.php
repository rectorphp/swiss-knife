<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ParentClassResolver;

use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Tests\ParentClassResolver\Fixture\AbstractParentClass;
use Rector\SwissKnife\Tests\ParentClassResolver\Fixture\ParentClass;
use Rector\SwissKnife\Tests\ParentClassResolver\Fixture\ParentClassInSeparateNamespace;

final class ParentClassResolverTest extends AbstractTestCase
{
    private ParentClassResolver $parentClassResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parentClassResolver = $this->make(ParentClassResolver::class);
    }

    public function test(): void
    {
        $parentClasses = $this->parentClassResolver->resolve(
            PhpFilesFinder::find([__DIR__ . '/Fixture']),
            static function (): void {
            }
        );

        $this->assertSame(
            [AbstractParentClass::class, ParentClass::class, ParentClassInSeparateNamespace::class],
            $parentClasses
        );
    }
}
