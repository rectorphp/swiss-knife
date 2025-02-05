<?php

namespace Rector\SwissKnife\Tests\Behastan\Behastan;

use Rector\SwissKnife\Behastan\Behastan;
use Rector\SwissKnife\Behastan\Finder\BehatMetafilesFinder;
use Rector\SwissKnife\Behastan\ValueObject\Mask\AbstractMask;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BehastanTest extends AbstractTestCase
{
    private Behastan $behastan;

    private BehatMetafilesFinder $behatMetafilesFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->behastan = $this->make(Behastan::class);
        $this->behatMetafilesFinder = $this->make(BehatMetafilesFinder::class);

        // silence output in tests
        $symfonyStyle = $this->make(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(Output::VERBOSITY_QUIET);
    }

    public function test(): void
    {
        $featureFiles = $this->behatMetafilesFinder->findFeatureFiles([__DIR__ . '/Fixture']);
        $contextFiles = $this->behatMetafilesFinder->findContextFiles([__DIR__ . '/Fixture']);

        $this->assertCount(1, $featureFiles);
        $this->assertCount(1, $contextFiles);

        $unusedMasks = $this->behastan->analyse($contextFiles, $featureFiles);
        $this->assertCount(1, $unusedMasks);
        $this->assertContainsOnlyInstancesOf(AbstractMask::class, $unusedMasks);

        /** @var AbstractMask $unusedMask */
        $unusedMask = $unusedMasks[0];
        $this->assertSame(__DIR__ . '/Fixture/BehatContext.php', $unusedMask->filePath);
        $this->assertSame('never used', $unusedMask->mask);
    }
}
