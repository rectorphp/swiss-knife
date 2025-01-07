<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstantFetchFinder;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

final class ClassConstantFetchFinderTest extends AbstractTestCase
{
    private ClassConstantFetchFinder $classConstantFetchFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classConstantFetchFinder = $this->make(ClassConstantFetchFinder::class);
    }

    public function testSkipInterfaceTraitAndEnum(): void
    {
        $classConstantFetches = $this->findInDirectory(__DIR__ . '/Fixture/Skip');
        $this->assertCount(0, $classConstantFetches);
    }

    public function test(): void
    {
        $classConstantFetches = $this->findInDirectory(__DIR__ . '/Fixture/Standard');
        $this->assertCount(2, $classConstantFetches);

        $firstClassConstantFetch = $classConstantFetches[0];
        $this->assertInstanceOf(CurrentClassConstantFetch::class, $firstClassConstantFetch);

        $secondClassConstantFetch = $classConstantFetches[1];
        $this->assertInstanceOf(ExternalClassAccessConstantFetch::class, $secondClassConstantFetch);
    }

    public function testParseError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Could not parse file "' . __DIR__ . '/Fixture/Error/ParseError.php": Syntax error, unexpected T_STRING on line 6'
        );

        $directory = __DIR__ . '/Fixture/Error';
        $progressBar = new ProgressBar(new NullOutput());
        $fileInfos = PhpFilesFinder::find([$directory]);
        $this->classConstantFetchFinder->find($fileInfos, $progressBar, false);
    }

    /**
     * @return ClassConstantFetchInterface[]
     */
    private function findInDirectory(string $directory): array
    {
        $progressBar = new ProgressBar(new NullOutput());
        $fileInfos = PhpFilesFinder::find([$directory]);

        return $this->classConstantFetchFinder->find($fileInfos, $progressBar, false);
    }
}
