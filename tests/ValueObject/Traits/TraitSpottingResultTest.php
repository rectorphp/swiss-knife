<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ValueObject\Traits;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\ValueObject\Traits\TraitMetadata;
use Rector\SwissKnife\ValueObject\Traits\TraitSpottingResult;

final class TraitSpottingResultTest extends TestCase
{
    public function test(): void
    {
        $firstTraitMetadata = new TraitMetadata(__DIR__ . '/../../Traits/Fixture/SomeTrait.php', 'SomeTrait');
        $firstTraitMetadata->markUsedIn(__DIR__ . '/../../Traits/Fixture/TraitUser.php');

        $secondTraitMetadata = new TraitMetadata(__DIR__ . '/../../Traits/Fixture/AnotherTrait.php', 'AnotherTrait');

        $traitSpottingResult = new TraitSpottingResult([$firstTraitMetadata, $secondTraitMetadata]);

        $this->assertSame(2, $traitSpottingResult->getTraitCount());

        $traitFilePaths = $traitSpottingResult->getTraitFilePaths();
        $this->assertCount(2, $traitFilePaths);
        $this->assertSame(
            [
                __DIR__ . '/../../Traits/Fixture/AnotherTrait.php',
                __DIR__ . '/../../Traits/Fixture/SomeTrait.php',
            ],
            $traitFilePaths
        );

        $lazyTraits = $traitSpottingResult->getTraitMaximumUsedTimes(1);
        $this->assertCount(1, $lazyTraits);
        $this->assertSame('SomeTrait', $lazyTraits[0]->getShortTraitName());

        $this->assertSame([], $traitSpottingResult->getTraitMaximumUsedTimes(0));
    }
}
