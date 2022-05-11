<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220511\Symplify\SmartFileSystem\SmartFileInfo;
final class FileError implements \Symplify\EasyCI\Contract\ValueObject\FileErrorInterface
{
    /**
     * @var string
     */
    private $errorMessage;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    public function __construct(string $errorMessage, \EasyCI20220511\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $this->errorMessage = $errorMessage;
        $this->smartFileInfo = $smartFileInfo;
    }
    public function getErrorMessage() : string
    {
        return $this->errorMessage;
    }
    public function getRelativeFilePath() : string
    {
        return $this->smartFileInfo->getRelativeFilePath();
    }
}
