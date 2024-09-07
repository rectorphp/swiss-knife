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
        $this->assertCount(2, $classConstantFetches);

        $firstClassConstant = $classConstantFetches[0];
        $this->assertSame(
            \Rector\SwissKnife\Tests\YAML\YamlConfigConstantExtractor\Fixture\SomeClassWithConstant::class,
            $firstClassConstant->getClassName()
        );
        $this->assertSame('USE_ME_IN_YAML', $firstClassConstant->getConstantName());

        $secondClassConstant = $classConstantFetches[1];
        $this->assertSame(
            \Rector\SwissKnife\Tests\YAML\YamlConfigConstantExtractor\Fixture\SomeClassWithConstant::class,
            $secondClassConstant->getClassName()
        );
        $this->assertSame('NO_REAL', $secondClassConstant->getConstantName());
    }
}
