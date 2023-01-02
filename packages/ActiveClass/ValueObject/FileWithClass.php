<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\ValueObject;

use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
final class FileWithClass
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $fileInfo;
    /**
     * @var string
     */
    private $className;
    public function __construct(SmartFileInfo $fileInfo, string $className)
    {
        $this->fileInfo = $fileInfo;
        $this->className = $className;
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function getRelativeFilepath() : string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd();
    }
}
