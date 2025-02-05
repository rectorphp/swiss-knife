<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject\Traits;

use SwissKnife202502\Nette\Utils\FileSystem;
final class TraitMetadata
{
    /**
     * @readonly
     * @var string
     */
    private $filePath;
    /**
     * @var string
     */
    private $shortTraitName;
    /**
     * @var int
     */
    private $lineCount;
    /**
     * @var string[]
     */
    private $usedIn = [];
    public function __construct(string $filePath, string $shortTraitName)
    {
        $this->filePath = $filePath;
        $this->shortTraitName = $shortTraitName;
        $this->lineCount = \substr_count(FileSystem::read($filePath), \PHP_EOL);
    }
    public function getShortTraitName() : string
    {
        return $this->shortTraitName;
    }
    public function getFilePath() : string
    {
        return $this->filePath;
    }
    public function markUsedIn(string $filePath) : void
    {
        $this->usedIn[] = $filePath;
    }
    /**
     * @return string[]
     */
    public function getUsedIn() : array
    {
        return $this->usedIn;
    }
    public function getUsedInCount() : int
    {
        return \count($this->usedIn);
    }
    public function getLineCount() : int
    {
        return $this->lineCount;
    }
}
