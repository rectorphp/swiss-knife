<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Latte\Contract;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220416\Symplify\SmartFileSystem\SmartFileInfo;
interface LatteTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array;
}
