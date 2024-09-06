<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Twig\TwigTemplateConstantExtractor;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Tests\Twig\TwigTemplateConstantExtractor\Fixture\SomeTemplateUsedConstant;
use Rector\SwissKnife\Twig\TwigTemplateConstantExtractor;

final class TwigTemplateConstantExtractorTest extends AbstractTestCase
{
    private TwigTemplateConstantExtractor $twigTemplateConstantExtractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twigTemplateConstantExtractor = $this->make(TwigTemplateConstantExtractor::class);
    }

    public function test(): void
    {
        $classConstantFetches = $this->twigTemplateConstantExtractor->extractFromDirs([__DIR__ . '/Fixture/twig']);
        $this->assertCount(2, $classConstantFetches);

        $this->assertContainsOnlyInstancesOf(ClassConstantFetchInterface::class, $classConstantFetches);

        /** @var ClassConstantFetchInterface $firstClassConstantFetch */
        $firstClassConstantFetch = $classConstantFetches[0];

        $this->assertSame(SomeTemplateUsedConstant::class, $firstClassConstantFetch->getClassName());
        $this->assertSame('STAY_PUBLIC', $firstClassConstantFetch->getConstantName());
    }
}
