<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220520\Symplify\SmartFileSystem\SmartFileInfo;
final class LineAwareFileError implements \Symplify\EasyCI\Contract\ValueObject\FileErrorInterface
{
    /**
     * @var string
     */
    private $errorMessage;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    /**
     * @var int
     */
    private $line;
    public function __construct(string $errorMessage, \EasyCI20220520\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, int $line)
    {
        $this->errorMessage = $errorMessage;
        $this->smartFileInfo = $smartFileInfo;
        $this->line = $line;
    }
    public function getErrorMessage() : string
    {
        return $this->errorMessage;
    }
    public function getRelativeFilePath() : string
    {
        $relativeFilePath = $this->smartFileInfo->getRelativeFilePath();
        return $relativeFilePath . ':' . $this->line;
    }
}
