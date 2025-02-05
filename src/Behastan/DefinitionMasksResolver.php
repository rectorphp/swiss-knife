<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Behastan;

use SwissKnife202502\Nette\Utils\Strings;
use Rector\SwissKnife\Behastan\ValueObject\ExactMask;
use Rector\SwissKnife\Behastan\ValueObject\MaskCollection;
use Rector\SwissKnife\Behastan\ValueObject\NamedMask;
use Rector\SwissKnife\Behastan\ValueObject\RegexMask;
use Rector\SwissKnife\Behastan\ValueObject\SkippedMask;
use Symfony\Component\Finder\SplFileInfo;
final class DefinitionMasksResolver
{
    /**
     * @var string
     */
    private const INSTRUCTION_DOCBLOCK_REGEX = '#\\@(Given|Then|When)\\s+(?<instruction>.*?)\\n#m';
    /**
     * @var string
     */
    private const INSTRUCTION_ATTRIBUTE_REGEX = '#\\#\\[(Given|Then|When)\\(\'(?<instruction>.*?)\'\\)#sm';
    /**
     * @param SplFileInfo[] $contextFiles
     */
    public function resolve(array $contextFiles) : MaskCollection
    {
        $masks = [];
        $rawMasksByFilePath = $this->resolveMasksFromFiles($contextFiles);
        foreach ($rawMasksByFilePath as $filePath => $rawMasks) {
            foreach ($rawMasks as $rawMask) {
                // @todo edge case - handle next
                if (\strpos($rawMask, ' [:') !== \false) {
                    $masks[] = new SkippedMask($rawMask, $filePath);
                    continue;
                }
                // regex pattern, handled else-where
                if (\strncmp($rawMask, '/', \strlen('/')) === 0) {
                    $masks[] = new RegexMask($rawMask, $filePath);
                    continue;
                }
                // handled in mask one
                if (Strings::match($rawMask, '#(\\:[\\W\\w]+)#')) {
                    //                if (str_contains($rawMask, ':')) {
                    $masks[] = new NamedMask($rawMask, $filePath);
                    continue;
                }
                $masks[] = new ExactMask($rawMask, $filePath);
            }
        }
        return new MaskCollection($masks);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     *
     * @return array<string, string[]>
     */
    private function resolveMasksFromFiles(array $fileInfos) : array
    {
        $masksByFilePath = [];
        foreach ($fileInfos as $fileInfo) {
            $matches = $this->matchDocblockAndAttributeDefinitions($fileInfo);
            foreach ($matches as $match) {
                $mask = \trim((string) $match['instruction']);
                // clear extra quote escaping that would cause miss-match with feature masks
                $mask = \str_replace('\\\'', "'", $mask);
                $mask = \str_replace('\\/', '/', $mask);
                $masksByFilePath[$fileInfo->getRealPath()][] = $mask;
            }
        }
        return $masksByFilePath;
    }
    /**
     * @return mixed[]
     */
    private function matchDocblockAndAttributeDefinitions(SplFileInfo $contextFileInfo) : array
    {
        $attributeMatches = Strings::matchAll($contextFileInfo->getContents(), self::INSTRUCTION_ATTRIBUTE_REGEX);
        $docblockMatches = Strings::matchAll($contextFileInfo->getContents(), self::INSTRUCTION_DOCBLOCK_REGEX);
        return \array_merge($attributeMatches, $docblockMatches);
    }
}
