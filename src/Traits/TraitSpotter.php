<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Traits;

use SwissKnife202506\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\TraitFilesFinder;
use Rector\SwissKnife\ValueObject\Traits\TraitMetadata;
use Rector\SwissKnife\ValueObject\Traits\TraitSpottingResult;
/**
 * @see \Rector\SwissKnife\Tests\Traits\TraitSpotterTest
 */
final class TraitSpotter
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Finder\TraitFilesFinder
     */
    private $traitFilesFinder;
    public function __construct(TraitFilesFinder $traitFilesFinder)
    {
        $this->traitFilesFinder = $traitFilesFinder;
    }
    /**
     * @param string[] $directories
     */
    public function analyse(array $directories) : TraitSpottingResult
    {
        $traitFiles = $this->traitFilesFinder->find($directories);
        $traitsMetadatas = [];
        foreach ($traitFiles as $traitFile) {
            $traitShortName = $traitFile->getBasename('.php');
            $traitsMetadatas[] = new TraitMetadata($traitFile->getRealPath(), $traitShortName);
        }
        $traitUsageFiles = $this->traitFilesFinder->findTraitUsages($directories);
        foreach ($traitUsageFiles as $traitUsageFile) {
            $matches = Strings::matchAll($traitUsageFile->getContents(), '#^    use (?<short_trait_name>[\\w]+);#m');
            foreach ($matches as $match) {
                $shortTraitName = $match['short_trait_name'];
                // fuzzy in exchange for speed
                $currentTraitMetadata = $this->matchTraitByShortName($traitsMetadatas, $shortTraitName);
                if (!$currentTraitMetadata instanceof TraitMetadata) {
                    continue;
                }
                $currentTraitMetadata->markUsedIn($traitUsageFile->getRealPath());
            }
        }
        return new TraitSpottingResult($traitsMetadatas);
    }
    /**
     * @param TraitMetadata[] $traitsMetadatas
     */
    private function matchTraitByShortName(array $traitsMetadatas, string $shortTraitName) : ?TraitMetadata
    {
        foreach ($traitsMetadatas as $traitMetadata) {
            if ($traitMetadata->getShortTraitName() === $shortTraitName) {
                return $traitMetadata;
            }
        }
        return null;
    }
}
