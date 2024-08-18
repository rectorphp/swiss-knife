<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\EntityClassResolver;

use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Tests\EntityClassResolver\Fixture\Anything\SomeAttributeDocument;
use Rector\SwissKnife\Tests\EntityClassResolver\Fixture\Anything\SomeDocument;
use Rector\SwissKnife\Tests\EntityClassResolver\Fixture\Entity\SomeEntity;

final class EntityClassResolverTest extends AbstractTestCase
{
    private EntityClassResolver $entityClassResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityClassResolver = $this->make(EntityClassResolver::class);
    }

    public function test(): void
    {
        $entityClasses = $this->entityClassResolver->resolve(
            [__DIR__ . '/Fixture/Entity', __DIR__ . '/Fixture/config'],
            static function (): void {
            }
        );

        $this->assertSame(
            ['App\Some\Entity\Conference', 'App\Some\Entity\Project', 'App\Some\Entity\Talk', SomeEntity::class],
            $entityClasses
        );
    }

    public function testODMDocument(): void
    {
        $documentClasses = $this->entityClassResolver->resolve(
            [__DIR__ . '/Fixture/Anything'],
            static function (): void {
            }
        );

        $this->assertSame([SomeAttributeDocument::class, SomeDocument::class], $documentClasses);
    }
}
