<?php

declare (strict_types=1);
namespace EasyCI20220530\Symplify\VendorPatches\ValueObject;

use EasyCI20220530\Symplify\SmartFileSystem\SmartFileInfo;
final class OldAndNewFileInfo
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $oldFileInfo;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $newFileInfo;
    /**
     * @var string
     */
    private $packageName;
    public function __construct(\EasyCI20220530\Symplify\SmartFileSystem\SmartFileInfo $oldFileInfo, \EasyCI20220530\Symplify\SmartFileSystem\SmartFileInfo $newFileInfo, string $packageName)
    {
        $this->oldFileInfo = $oldFileInfo;
        $this->newFileInfo = $newFileInfo;
        $this->packageName = $packageName;
    }
    public function getOldFileInfo() : \EasyCI20220530\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->oldFileInfo;
    }
    public function getOldFileRelativePath() : string
    {
        return $this->oldFileInfo->getRelativeFilePathFromCwd();
    }
    public function getNewFileRelativePath() : string
    {
        return $this->newFileInfo->getRelativeFilePathFromCwd();
    }
    public function getNewFileInfo() : \EasyCI20220530\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->newFileInfo;
    }
    public function isContentIdentical() : bool
    {
        return $this->newFileInfo->getContents() === $this->oldFileInfo->getContents();
    }
    public function getPackageName() : string
    {
        return $this->packageName;
    }
}
