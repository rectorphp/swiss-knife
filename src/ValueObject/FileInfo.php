<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use Entropy\Utils\FileSystem;
use SplFileInfo;

final class FileInfo extends SplFileInfo
{
    public function __construct(
        string $filePath,
        private readonly string $relativePath = '',
        private readonly string $relativePathname = '',
    ) {
        parent::__construct($filePath);
    }

    public function getContents(): string
    {
        return FileSystem::read($this->getPathname());
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function getRelativePathname(): string
    {
        return $this->relativePathname;
    }
}
