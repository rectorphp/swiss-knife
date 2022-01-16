<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Git;

use EasyCI20220116\Nette\Utils\Strings;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Git\ConflictResolver\ConflictResolverTest
 */
final class ConflictResolver
{
    /**
     * @see https://regex101.com/r/iYPxCV/1
     * @var string
     */
    private const CONFLICT_REGEX = '#^<<<<<<<<#';
    public function extractFromFileInfo(\EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : int
    {
        $conflictsMatch = \EasyCI20220116\Nette\Utils\Strings::matchAll($fileInfo->getContents(), self::CONFLICT_REGEX);
        return \count($conflictsMatch);
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return int[]
     */
    public function extractFromFileInfos(array $fileInfos) : array
    {
        $conflictCountsByFilePath = [];
        foreach ($fileInfos as $fileInfo) {
            $conflictCount = $this->extractFromFileInfo($fileInfo);
            if ($conflictCount === 0) {
                continue;
            }
            // test fixtures, that should be ignored
            if ($fileInfo->getRealPath() === \realpath(__DIR__ . '/../../tests/Git/ConflictResolver/Fixture/some_file.txt')) {
                continue;
            }
            $conflictCountsByFilePath[$fileInfo->getRelativeFilePathFromCwd()] = $conflictCount;
        }
        return $conflictCountsByFilePath;
    }
}
