<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Behastan;

use SwissKnife202502\Nette\Utils\Strings;
use Rector\SwissKnife\Behastan\ValueObject\Mask\AbstractMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\ExactMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\NamedMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\RegexMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\SkippedMask;
use Rector\SwissKnife\Behastan\ValueObject\MaskCollection;
use SwissKnife202502\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202502\Symfony\Component\Finder\SplFileInfo;
/**
 * @see \Rector\SwissKnife\Tests\Behastan\Behastan\BehastanTest
 */
final class Behastan
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Behastan\DefinitionMasksResolver
     */
    private $definitionMasksResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Behastan\UsedInstructionResolver
     */
    private $usedInstructionResolver;
    public function __construct(SymfonyStyle $symfonyStyle, \Rector\SwissKnife\Behastan\DefinitionMasksResolver $definitionMasksResolver, \Rector\SwissKnife\Behastan\UsedInstructionResolver $usedInstructionResolver)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->definitionMasksResolver = $definitionMasksResolver;
        $this->usedInstructionResolver = $usedInstructionResolver;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return AbstractMask[]
     */
    public function analyse(array $contextFiles, array $featureFiles) : array
    {
        $maskCollection = $this->definitionMasksResolver->resolve($contextFiles);
        $this->printStats($maskCollection);
        $featureInstructions = $this->usedInstructionResolver->resolveInstructionsFromFeatureFiles($featureFiles);
        $maskProgressBar = $this->symfonyStyle->createProgressBar($maskCollection->count());
        $unusedMasks = [];
        foreach ($maskCollection->all() as $mask) {
            $maskProgressBar->advance();
            if ($this->isMaskUsed($mask, $featureInstructions)) {
                continue;
            }
            $unusedMasks[] = $mask;
        }
        $maskProgressBar->finish();
        return $unusedMasks;
    }
    private function printStats(MaskCollection $maskCollection) : void
    {
        $this->symfonyStyle->writeln(\sprintf('Found %d masks:', $maskCollection->count()));
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->writeln(\sprintf(' * %d exact', $maskCollection->countByType(ExactMask::class)));
        $this->symfonyStyle->writeln(\sprintf(' * %d /regex/', $maskCollection->countByType(RegexMask::class)));
        $this->symfonyStyle->writeln(\sprintf(' * %d :named', $maskCollection->countByType(NamedMask::class)));
        $this->symfonyStyle->writeln(\sprintf(' * %d skipped', $maskCollection->countByType(SkippedMask::class)));
        $skippedMasks = $maskCollection->byType(SkippedMask::class);
        if ($skippedMasks !== []) {
            $this->symfonyStyle->newLine();
            foreach ($skippedMasks as $skippedMask) {
                $this->printMask($skippedMask);
            }
            $this->symfonyStyle->newLine();
        }
    }
    /**
     * @param string[] $featureInstructions
     */
    private function isRegexDefinitionUsed(string $regexBehatDefinition, array $featureInstructions) : bool
    {
        foreach ($featureInstructions as $featureInstruction) {
            if (Strings::match($featureInstruction, $regexBehatDefinition)) {
                // it is used!
                return \true;
            }
        }
        return \false;
    }
    private function printMask(AbstractMask $unusedMask) : void
    {
        $this->symfonyStyle->writeln($unusedMask->mask);
        // make path relative
        $relativeFilePath = \str_replace(\getcwd() . '/', '', $unusedMask->filePath);
        $this->symfonyStyle->writeln($relativeFilePath);
        $this->symfonyStyle->newLine();
    }
    /**
     * @param string[] $featureInstructions
     */
    private function isMaskUsed(AbstractMask $mask, array $featureInstructions) : bool
    {
        if ($mask instanceof SkippedMask) {
            return \true;
        }
        // is used?
        if ($mask instanceof ExactMask && \in_array($mask->mask, $featureInstructions, \true)) {
            return \true;
        }
        // is used?
        if ($mask instanceof RegexMask && $this->isRegexDefinitionUsed($mask->mask, $featureInstructions)) {
            return \true;
        }
        if ($mask instanceof NamedMask) {
            // normalize :mask definition to regex
            $regexMask = '#' . Strings::replace($mask->mask, '#(\\:[\\W\\w]+)#', '(.*?)') . '#';
            if ($this->isRegexDefinitionUsed($regexMask, $featureInstructions)) {
                return \true;
            }
        }
        return \false;
    }
}
