<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Tests\Finder\SrcTestsDirectoriesFinder;

use Migrify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Migrify\EasyCI\HttpKernel\EasyCIKernel;
use Migrify\EasyCI\ValueObject\SrcAndTestsDirectories;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SrcTestsDirectoriesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var SrcTestsDirectoriesFinder
     */
    private $srcTestsDirectoriesFinder;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->srcTestsDirectoriesFinder = self::$container->get(SrcTestsDirectoriesFinder::class);
    }

    public function test(): void
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories(
            [__DIR__ . '/Fixture/only_test'],
            true
        );

        $this->assertNotNull($srcAndTestsDirectories);

        /** @var SrcAndTestsDirectories $srcAndTestsDirectories */
        $this->assertCount(0, $srcAndTestsDirectories->getRelativePathSrcDirectories());
        $this->assertCount(1, $srcAndTestsDirectories->getRelativePathTestsDirectories());
    }

    public function testNothing(): void
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories(
            [__DIR__ . '/Fixture/nothing']
        );
        $this->assertNull($srcAndTestsDirectories);
    }
}
