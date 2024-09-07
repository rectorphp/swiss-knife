<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\YAML\YamlConfigConstantExtractor;

use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\YAML\YamlConfigConstantExtractor;

final class YamlConfigConstantExtractorTest extends AbstractTestCase
{
    private YamlConfigConstantExtractor $yamlConfigConstantExtractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->yamlConfigConstantExtractor = $this->make(YamlConfigConstantExtractor::class);
    }

    public function test(): void
    {
        $classConstantFetches = $this->yamlConfigConstantExtractor->extractFromDirs([__DIR__ . '/Fixture']);
        $this->assertCount(1, $classConstantFetches);
    }
}
