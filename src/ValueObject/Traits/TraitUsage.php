<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject\Traits;

final class TraitUsage
{
    /**
     * @var string
     */
    public $shortTraitName;
    /**
     * @var int
     */
    public $lineCount;
    /**
     * @var string[]
     */
    public $usingFiles;
    /**
     * @param string[] $usingFiles
     */
    public function __construct(string $shortTraitName, int $lineCount, array $usingFiles)
    {
        $this->shortTraitName = $shortTraitName;
        $this->lineCount = $lineCount;
        $this->usingFiles = $usingFiles;
    }
}
