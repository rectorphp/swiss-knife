<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;

use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\ProgressBar;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;

final class ClassConstantFetchFinderDebugTest extends TestCase
{
    public function testDebugMode(): void
    {
        $containerFactory = new \Rector\SwissKnife\DependencyInjection\ContainerFactory();
        $container = $containerFactory->create();
        $finder = $container->make(ClassConstantFetchFinder::class);

        $fileInfos = PhpFilesFinder::find([
            __DIR__ . '/../../Finder/ClassConstantFetchFinder/Fixture/Standard',
        ]);

        $progressBar = new ProgressBar(new OutputColorizer());
        $progressBar->start(1);

        $fetches = $finder->find($fileInfos, $progressBar, isDebug: true);

        $this->assertNotEmpty($fetches);
    }
}
