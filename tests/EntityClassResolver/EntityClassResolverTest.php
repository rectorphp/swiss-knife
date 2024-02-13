<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\EntityClassResolver;

use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
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
        $fileInfos = PhpFilesFinder::findPhpFileInfos([__DIR__ . '/Fixture']);
        $entityClasses = $this->entityClassResolver->resolve($fileInfos, function () {
        });

        $this->assertSame([SomeEntity::class], $entityClasses);
    }
}
