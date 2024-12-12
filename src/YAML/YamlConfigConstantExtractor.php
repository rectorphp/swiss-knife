<?php

declare (strict_types=1);
namespace Rector\SwissKnife\YAML;

use SwissKnife202412\Nette\Utils\FileSystem;
use SwissKnife202412\Nette\Utils\Strings;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
/**
 * @see \Rector\SwissKnife\Tests\YAML\YamlConfigConstantExtractor\YamlConfigConstantExtractorTest
 */
final class YamlConfigConstantExtractor
{
    /**
     * @param string[] $directories
     * @return ClassConstantFetchInterface[]
     */
    public function extractFromDirs(array $directories) : array
    {
        $yamlFileInfos = FilesFinder::findYamlFiles($directories);
        $classConstantFetches = [];
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $currentClassConstantFetches = $this->findClassConstantFetchesInFile($yamlFileInfo->getRealPath());
            $classConstantFetches = \array_merge($classConstantFetches, $currentClassConstantFetches);
        }
        return $classConstantFetches;
    }
    /**
     * @return ClassConstantFetchInterface[]
     */
    private function findClassConstantFetchesInFile(string $filePath) : array
    {
        $fileContents = FileSystem::read($filePath);
        // find constant fetches in YAML file
        $constantMatches = Strings::matchAll($fileContents, '#\\b(?<class>[\\w\\\\]+)::(?<constant>\\w+)\\b#');
        $externalClassAccessConstantFetches = [];
        foreach ($constantMatches as $constantMatch) {
            $externalClassAccessConstantFetches[] = new ExternalClassAccessConstantFetch($constantMatch['class'], $constantMatch['constant']);
        }
        return $externalClassAccessConstantFetches;
    }
}
