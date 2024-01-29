<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Git\ConflictResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\Git\ConflictResolver;

final class ConflictResolverTest extends TestCase
{
    private ConflictResolver $conflictResolver;

    protected function setUp(): void
    {
        $this->conflictResolver = new ConflictResolver();
    }

    #[DataProvider('provideData')]
    public function test(string $filePath, int $expectedConflictCount): void
    {
        $unresolvedConflictCount = $this->conflictResolver->extractFromFileInfo($filePath);
        $this->assertSame($expectedConflictCount, $unresolvedConflictCount);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/some_file.txt', 1];
        yield [__DIR__ . '/Fixture/some_other_file.txt', 1];
        yield [__DIR__ . '/Fixture/correct_file.txt', 0];
    }
}
