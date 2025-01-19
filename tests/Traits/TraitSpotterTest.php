<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Traits;

use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Traits\TraitSpotter;
use Rector\SwissKnife\ValueObject\Traits\TraitUsage;

final class TraitSpotterTest extends AbstractTestCase
{
    public function test(): void
    {
        $traitSpotter = $this->make(TraitSpotter::class);

        $traitSpottingResult = $traitSpotter->analyse([__DIR__ . '/Fixture/']);
        $this->assertSame(2, $traitSpottingResult->getTraitCount());

        $onceUsedTraits = $traitSpottingResult->getTraitMaximumUsedTimes(1);
        $this->assertCount(1, $onceUsedTraits);

        $onceTraitUsage = $onceUsedTraits[0];
        $this->assertInstanceOf(TraitUsage::class, $onceTraitUsage);

        $this->assertSame('SomeTrait', $onceTraitUsage->shortTraitName);
        $this->assertSame(7, $onceTraitUsage->lineCount);
        $this->assertSame(['tests/Traits/Fixture/TraitUser.php'], $onceTraitUsage->usingFiles);
    }
}
