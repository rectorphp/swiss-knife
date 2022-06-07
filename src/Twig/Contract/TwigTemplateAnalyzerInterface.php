<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Twig\Contract;

use EasyCI20220607\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220607\Symplify\SmartFileSystem\SmartFileInfo;
interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array;
}
