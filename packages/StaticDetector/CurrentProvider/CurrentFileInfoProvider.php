<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\CurrentProvider;

use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentFileInfoProvider
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    public function setCurrentFileInfo(SmartFileInfo $smartFileInfo) : void
    {
        $this->smartFileInfo = $smartFileInfo;
    }
    public function getSmartFileInfo() : SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
