<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Git;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * @see \Rector\SwissKnife\Tests\Git\ConflictResolver\ConflictResolverTest
 */
final class ConflictResolver
{
    /**
     * @see https://regex101.com/r/L2CThC/1
     * @var string
     */
    private const CONFLICT_REGEX = '#^(<<<<<<<|>>>>>>>)#m';

    /**
     * @api
     */
    public function extractFromFileInfo(string $filePath): int
    {
        $fileContents = FileSystem::read($filePath);
        $conflictsMatch = Strings::matchAll($fileContents, self::CONFLICT_REGEX);

        return count($conflictsMatch);
    }

    /**
     * @param string[] $filePaths
     * @return int[]
     */
    public function extractFromFileInfos(array $filePaths): array
    {
        $conflictCountsByFilePath = [];

        foreach ($filePaths as $filePath) {
            $conflictCount = $this->extractFromFileInfo($filePath);
            if ($conflictCount === 0) {
                continue;
            }

            // test fixtures, that should be ignored
            if (str_contains((string) realpath($filePath), '/tests/Git/ConflictResolver/Fixture')) {
                continue;
            }

            $conflictCountsByFilePath[$filePath] = $conflictCount;
        }

        return $conflictCountsByFilePath;
    }
}
