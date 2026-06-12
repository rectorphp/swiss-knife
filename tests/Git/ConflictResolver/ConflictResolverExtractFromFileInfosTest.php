<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Git\ConflictResolver;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Git\ConflictResolver;

final class ConflictResolverExtractFromFileInfosTest extends TestCase
{
    private ConflictResolver $conflictResolver;

    protected function setUp(): void
    {
        $this->conflictResolver = new ConflictResolver();
    }

    public function testExtractFromFileInfosSkipsFixtures(): void
    {
        $fixturePath = __DIR__ . '/Fixture/some_file.txt';
        $outsidePath = sys_get_temp_dir() . '/swiss-knife-conflict-' . uniqid() . '.txt';
        file_put_contents($outsidePath, "<<<<<<< HEAD\ncontent\n=======\n");

        try {
            $result = $this->conflictResolver->extractFromFileInfos([$fixturePath, $outsidePath]);

            $this->assertArrayNotHasKey($fixturePath, $result);
            $this->assertSame([$outsidePath => 1], $result);
        } finally {
            @unlink($outsidePath);
        }
    }

    public function testExtractFromFileInfosSkipsFilesWithoutConflicts(): void
    {
        $correctFilePath = __DIR__ . '/Fixture/correct_file.txt';

        $this->assertSame([], $this->conflictResolver->extractFromFileInfos([$correctFilePath]));
    }
}
