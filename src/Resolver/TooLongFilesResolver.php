<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Resolver;

use SwissKnife202405\Symfony\Component\Finder\SplFileInfo;
final class TooLongFilesResolver
{
    /**
     * In windows the max-path length is 260 chars. we give a bit room for the path up to the rector project
     *
     * @var int
     */
    public const MAX_FILE_LENGTH = 200;
    /**
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    public function resolve(array $fileInfos) : array
    {
        return \array_filter($fileInfos, function (SplFileInfo $fileInfo) : bool {
            return $this->isFileContentLongerThan($fileInfo, self::MAX_FILE_LENGTH);
        });
    }
    private function isFileContentLongerThan(SplFileInfo $fileInfo, int $maxFileLenght) : bool
    {
        $filePathLength = \strlen($fileInfo->getRealPath());
        return $filePathLength > $maxFileLenght;
    }
}
