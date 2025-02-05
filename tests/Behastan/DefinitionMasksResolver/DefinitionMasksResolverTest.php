<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Behastan\DefinitionMasksResolver;

use Rector\SwissKnife\Behastan\DefinitionMasksResolver;
use Rector\SwissKnife\Behastan\Finder\BehatMetafilesFinder;
use Rector\SwissKnife\Behastan\ValueObject\Mask\ExactMask;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Tests\Behastan\DefinitionMasksResolver\Fixture\AnotherBehatContext;

final class DefinitionMasksResolverTest extends AbstractTestCase
{
    public function test(): void
    {
        $behatMetafilesFinder = $this->make(BehatMetafilesFinder::class);
        $contextFileInfos = $behatMetafilesFinder->findContextFiles([__DIR__ . '/Fixture']);

        $definitionMasksResolver = $this->make(DefinitionMasksResolver::class);

        $maskCollection = $definitionMasksResolver->resolve($contextFileInfos);

        $this->assertCount(2, $maskCollection->all());

        $exactMasks = $maskCollection->byType(ExactMask::class);
        $this->assertCount(2, $exactMasks);
        $this->assertContainsOnlyInstancesOf(ExactMask::class, $exactMasks);

        $firstExactMask = $exactMasks[0];
        $this->assertSame('I click homepage', $firstExactMask->mask);
        $this->assertSame(AnotherBehatContext::class, $firstExactMask->className);
        $this->assertSame(__DIR__ . '/Fixture/AnotherBehatContext.php', $firstExactMask->filePath);
    }
}
