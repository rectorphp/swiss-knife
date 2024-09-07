<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Twig;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;

/**
 * @see \Rector\SwissKnife\Tests\Twig\TwigTemplateConstantExtractor\TwigTemplateConstantExtractorTest
 */
final class TwigTemplateConstantExtractor
{
    /**
     * @param string[] $directories
     * @return ClassConstantFetchInterface[]
     */
    public function extractFromDirs(array $directories): array
    {
        $twigFileInfos = FilesFinder::findTwigFiles($directories);

        $classConstantFetches = [];
        foreach ($twigFileInfos as $twigFileInfo) {
            $currentClassConstantFetches = $this->findClassConstantFetchesInFile($twigFileInfo->getRealPath());

            $classConstantFetches = array_merge($classConstantFetches, $currentClassConstantFetches);
        }

        return $classConstantFetches;
    }

    /**
     * @return ClassConstantFetchInterface[]
     */
    private function findClassConstantFetchesInFile(string $filePath): array
    {
        $fileContents = FileSystem::read($filePath);

        $constantMatches = Strings::matchAll($fileContents, '#{{.*?\s*constant\(\s*([\'"])(?<constant>.*?)\1#');

        $externalClassAccessConstantFetches = [];
        foreach ($constantMatches as $constantMatch) {
            $constantMatchValue = $constantMatch['constant'];

            // global constant → skip
            if (! str_contains($constantMatchValue, '::')) {
                continue;
            }

            [$className, $constantName] = explode('::', $constantMatchValue);
            $className = str_replace('\\\\', '\\', $className);

            $externalClassAccessConstantFetches[] = new ExternalClassAccessConstantFetch($className, $constantName);
        }

        return $externalClassAccessConstantFetches;
    }

    //    /**
    //     * @param string[] $directories
    //     * @return SplFileInfo[]
    //     */
    //    private function findTwigFileInfosInDirectories(array $directories): array
    //    {
    //        Assert::allString($directories);
    //        Assert::allDirectory($directories);
    //
    //        $twigFinder = Finder::create()->files()->name('*.twig')
    //            ->in($directories);
    //
    //        return iterator_to_array($twigFinder->getIterator());
    //    }
}
