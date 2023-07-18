<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Git\ConflictResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCI\Git\ConflictResolver;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConflictResolverTest extends AbstractKernelTestCase
{
    private ConflictResolver $conflictResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->conflictResolver = $this->getService(ConflictResolver::class);
    }

    #[DataProvider('provideData')]
    public function test(SmartFileInfo $fileInfo, int $expectedConflictCount): void
    {
        $unresolvedConflictCount = $this->conflictResolver->extractFromFileInfo($fileInfo);
        $this->assertSame($expectedConflictCount, $unresolvedConflictCount);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public static function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/some_file.txt'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/some_other_file.txt'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/correct_file.txt'), 0];
    }
}
