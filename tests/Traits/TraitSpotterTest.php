<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Traits;

use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Traits\TraitSpotter;
use Rector\SwissKnife\ValueObject\Traits\TraitMetadata;

final class TraitSpotterTest extends AbstractTestCase
{
    public function test(): void
    {
        $traitSpotter = $this->make(TraitSpotter::class);

        $traitSpottingResult = $traitSpotter->analyse([__DIR__ . '/Fixture/']);
        $this->assertSame(2, $traitSpottingResult->getTraitCount());

        $onceUsedTraitMetadatas = $traitSpottingResult->getTraitMaximumUsedTimes(1);
        $this->assertCount(1, $onceUsedTraitMetadatas);

        $onceTraitUsage = $onceUsedTraitMetadatas[0];
        $this->assertInstanceOf(TraitMetadata::class, $onceTraitUsage);

        $this->assertSame('SomeTrait', $onceTraitUsage->getShortTraitName());
        $this->assertSame(7, $onceTraitUsage->getLineCount());
        $this->assertSame([__DIR__ . '/Fixture/TraitUser.php'], $onceTraitUsage->getUsedIn());
    }
}
