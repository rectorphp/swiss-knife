<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject\Traits;

final class TraitSpottingResult
{
    /**
     * @var array<string, int>
     * @readonly
     */
    private $shortTraitNamesToLineCount;
    /**
     * @var array<string, string[]>
     * @readonly
     */
    private $traitUsagesToFiles;
    /**
     * @param array<string, int> $shortTraitNamesToLineCount
     * @param array<string, string[]> $traitUsagesToFiles
     */
    public function __construct(array $shortTraitNamesToLineCount, array $traitUsagesToFiles)
    {
        $this->shortTraitNamesToLineCount = $shortTraitNamesToLineCount;
        $this->traitUsagesToFiles = $traitUsagesToFiles;
    }
    public function getTraitCount() : int
    {
        return \count($this->shortTraitNamesToLineCount);
    }
    /**
     * @return TraitUsage[]
     */
    public function getTraitMaximumUsedTimes(int $limit) : array
    {
        $traitUsages = [];
        foreach ($this->traitUsagesToFiles as $shortTraitName => $usingFiles) {
            // to many places
            if (\count($usingFiles) > $limit) {
                continue;
            }
            // probably external, nothing we can do about it
            if (!isset($this->shortTraitNamesToLineCount[$shortTraitName])) {
                continue;
            }
            $traitUsages[] = new \Rector\SwissKnife\ValueObject\Traits\TraitUsage($shortTraitName, $this->shortTraitNamesToLineCount[$shortTraitName], $usingFiles);
        }
        return $traitUsages;
    }
}
