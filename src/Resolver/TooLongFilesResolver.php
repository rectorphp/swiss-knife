<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Resolver;

use EasyCI20220607\Symplify\SmartFileSystem\SmartFileInfo;
final class TooLongFilesResolver
{
    /**
     * In windows the max-path length is 260 chars. we give a bit room for the path up to the rector project
     *
     * @var int
     */
    public const MAX_FILE_LENGTH = 200;
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function resolve(array $fileInfos) : array
    {
        return \array_filter($fileInfos, function (SmartFileInfo $fileInfo) : bool {
            return $this->isFileContentLongerThan($fileInfo, self::MAX_FILE_LENGTH);
        });
    }
    private function isFileContentLongerThan(SmartFileInfo $fileInfo, int $maxFileLenght) : bool
    {
        $filePathLength = \strlen($fileInfo->getRealPath());
        return $filePathLength > $maxFileLenght;
    }
}
