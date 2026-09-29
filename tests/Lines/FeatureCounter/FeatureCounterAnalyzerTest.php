<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Lines\FeatureCounter;

use Rector\SwissKnife\Lines\FeatureCounter\FeatureCounterAnalyzer;
use Rector\SwissKnife\Lines\Finder\ProjectFilesFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FeatureCounterAnalyzerTest extends AbstractTestCase
{
    private FeatureCounterAnalyzer $featureCounterAnalyzer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->featureCounterAnalyzer = $this->make(FeatureCounterAnalyzer::class);
    }

    public function test(): void
    {
        $fileInfos = ProjectFilesFinder::find(__DIR__ . '/Fixture');
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);

        foreach ($featureCollector->getPhpFeatures() as $phpFeature) {
            if ($phpFeature->getName() === 'Typed properties') {
                $this->assertSame(1, $phpFeature->getCount());
            }

            if ($phpFeature->getName() === 'Union types') {
                $this->assertSame(2, $phpFeature->getCount());
            }
        }
    }
}
