<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Git;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\EasyCI\Tests\Git\ConflictResolver\ConflictResolverTest
 */
final class ConflictResolver
{
    public function extractFromFileInfo(SmartFileInfo $fileInfo): int
    {
        $conflictsMatch = Strings::matchAll($fileInfo->getContents(), '#^<<<<<<<<#');

        return count($conflictsMatch);
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return int[]
     */
    public function extractFromFileInfos(array $fileInfos)
    {
        $conflictCountsByFilePath = [];

        foreach ($fileInfos as $fileInfo) {
            $conflictCount = $this->extractFromFileInfo($fileInfo);
            if ($conflictCount === 0) {
                continue;
            }

            $conflictCountsByFilePath[$fileInfo->getRelativeFilePathFromCwd()] = $conflictCount;
        }

        return $conflictCountsByFilePath;
    }
}
