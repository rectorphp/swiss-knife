<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject\Traits;

use Nette\Utils\FileSystem;

final class TraitMetadata
{
    private int $lineCount;

    /**
     * @var string[]
     */
    private array $usedIn = [];

    public function __construct(
        private readonly string $filePath,
        private string $shortTraitName
    ) {
        $this->lineCount = substr_count(FileSystem::read($filePath), PHP_EOL);
    }

    public function getShortTraitName(): string
    {
        return $this->shortTraitName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function markUsedIn(string $filePath): void
    {
        $this->usedIn[] = $filePath;
    }

    /**
     * @return string[]
     */
    public function getUsedIn(): array
    {
        return $this->usedIn;
    }

    public function getUsedInCount(): int
    {
        return count($this->usedIn);
    }

    public function getLineCount(): int
    {
        return $this->lineCount;
    }
}
