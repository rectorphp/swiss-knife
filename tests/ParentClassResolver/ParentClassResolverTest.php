<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ParentClassResolver;

use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Tests\ParentClassResolver\Fixture\SomeParentClass;

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
        $parentClasses = $this->parentClassResolver->resolve(PhpFilesFinder::find([__DIR__ . '/Fixture']), static function (): void {
        });

        $this->assertSame(
            [
                'ArrayObject',
                'Rector\SwissKnife\Tests\ParentClassResolver\Fixture\AbstractParentClass',
                'Rector\SwissKnife\Tests\ParentClassResolver\Fixture\ParentClass',
                'Rector\SwissKnife\Tests\ParentClassResolver\Fixture\ParentClassInSeparateNamespace',
                SomeParentClass::class,
                \SomeUnknownRootNamespaceClass::class, // @phpstan-ignore-line
            ],
            $parentClasses
        );
    }
}
