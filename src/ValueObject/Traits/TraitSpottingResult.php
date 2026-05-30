<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject\Traits;

final class TraitSpottingResult
{
    /**
     * @var TraitMetadata[]
     * @readonly
     */
    private $traitsMetadatas;
    /**
     * @param TraitMetadata[] $traitsMetadatas
     */
    public function __construct(array $traitsMetadatas)
    {
        $this->traitsMetadatas = $traitsMetadatas;
    }
    public function getTraitCount() : int
    {
        return \count($this->traitsMetadatas);
    }
    /**
     * @return TraitMetadata[]
     */
    public function getTraitMaximumUsedTimes(int $limit) : array
    {
        $usedTraitsMetadatas = [];
        foreach ($this->traitsMetadatas as $traitMetadata) {
            // not used at all, already handled by phpstan
            if ($traitMetadata->getUsedInCount() === 0) {
                continue;
            }
            // to many places
            if ($traitMetadata->getUsedInCount() > $limit) {
                continue;
            }
            $usedTraitsMetadatas[] = $traitMetadata;
        }
        return $usedTraitsMetadatas;
    }
    /**
     * @return string[]
     */
    public function getTraitFilePaths() : array
    {
        $traitFilePaths = [];
        foreach ($this->traitsMetadatas as $traitMetadata) {
            $traitFilePaths[] = $traitMetadata->getFilePath();
        }
        \sort($traitFilePaths);
        return $traitFilePaths;
    }
}
