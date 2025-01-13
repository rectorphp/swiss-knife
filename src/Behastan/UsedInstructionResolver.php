<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Behastan;

use SwissKnife202501\Nette\Utils\Strings;
use RuntimeException;
use SwissKnife202501\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202501\Webmozart\Assert\Assert;
final class UsedInstructionResolver
{
    /**
     * @param SplFileInfo[] $featureFileInfos
     * @return string[]
     */
    public function resolveInstructionsFromFeatureFiles(array $featureFileInfos) : array
    {
        Assert::allIsInstanceOf($featureFileInfos, SplFileInfo::class);
        $instructions = [];
        foreach ($featureFileInfos as $featureFileInfo) {
            $matches = Strings::matchAll($featureFileInfo->getContents(), '#\\s+(Given|When|And|Then)\\s+(?<instruction>.*?)\\n#m');
            if ($matches === []) {
                // there should be at least one instruction in each feature file
                throw new RuntimeException(\sprintf('Unable to resolve instructions from %s file', $featureFileInfo->getRealPath()));
            }
            foreach ($matches as $match) {
                $instructions[] = \trim((string) $match['instruction']);
            }
        }
        return $instructions;
    }
}
