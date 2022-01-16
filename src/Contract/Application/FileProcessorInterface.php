<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Contract\Application;

use EasyCI20220116\Symplify\EasyCI\ValueObject\FileError;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileError[]
     */
    public function processFileInfos(array $fileInfos) : array;
}
