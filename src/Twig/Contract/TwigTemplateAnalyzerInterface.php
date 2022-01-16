<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Twig\Contract;

use EasyCI20220116\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array;
}
