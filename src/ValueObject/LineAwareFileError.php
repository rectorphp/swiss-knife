<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LineAwareFileError implements FileErrorInterface
{
    public function __construct(
        private readonly string $errorMessage,
        private readonly SmartFileInfo $smartFileInfo,
        private readonly int $line
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getRelativeFilePath(): string
    {
        $relativeFilePath = $this->smartFileInfo->getRelativeFilePath();
        return $relativeFilePath . ':' . $this->line;
    }
}
