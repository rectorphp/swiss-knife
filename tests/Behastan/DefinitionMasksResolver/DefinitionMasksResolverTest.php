<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Behastan\DefinitionMasksResolver;

use Rector\SwissKnife\Behastan\DefinitionMasksResolver;
use Rector\SwissKnife\Behastan\Finder\BehatMetafilesFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class DefinitionMasksResolverTest extends AbstractTestCase
{
    public function test(): void
    {
        $behatMetafilesFinder = $this->make(BehatMetafilesFinder::class);
        $contextFileInfos = $behatMetafilesFinder->findContextFiles([__DIR__ . '/Fixture']);

        $definitionMasksResolver = $this->make(DefinitionMasksResolver::class);

        $maskCollection = $definitionMasksResolver->resolve($contextFileInfos);

        dump($maskCollection);
        die;
    }
}
