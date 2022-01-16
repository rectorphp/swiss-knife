<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Config\Contract;

use EasyCI20220116\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
interface ConfigFileAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos) : array;
}
