<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Traits;

use SwissKnife202501\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\TraitFilesFinder;
use Rector\SwissKnife\ValueObject\Traits\TraitSpottingResult;
use SwissKnife202501\Symfony\Component\Finder\SplFileInfo;
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
        $shortNameToLineCount = [];
        foreach ($traitFiles as $traitFile) {
            $traitShortName = $traitFile->getBasename('.php');
            $shortNameToLineCount[$traitShortName] = \substr_count($traitFile->getContents(), \PHP_EOL);
        }
        $shortTraitNamesToLineCount = $shortNameToLineCount;
        $traitUsageFiles = $this->traitFilesFinder->findTraitUsages($directories);
        $usagesToFiles = [];
        foreach ($traitUsageFiles as $traitUsageFile) {
            $matches = Strings::matchAll($traitUsageFile->getContents(), '#    use (?<short_trait_name>[\\w]+);#');
            foreach ($matches as $match) {
                $shortTraitName = $match['short_trait_name'];
                $usagesToFiles[$shortTraitName][] = $this->getRelativeFilePath($traitUsageFile);
            }
        }
        $traitUsagesToFiles = $usagesToFiles;
        return new TraitSpottingResult($shortTraitNamesToLineCount, $traitUsagesToFiles);
    }
    private function getRelativeFilePath(SplFileInfo $fileInfo) : string
    {
        return \substr($fileInfo->getRealPath(), \strlen((string) \getcwd()) + 1);
    }
}
