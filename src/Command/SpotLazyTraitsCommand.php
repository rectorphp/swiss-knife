<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\Traits\TraitSpotter;
final class SpotLazyTraitsCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Traits\TraitSpotter
     */
    private $traitSpotter;
    public function __construct(OutputPrinter $outputPrinter, TraitSpotter $traitSpotter)
    {
        $this->outputPrinter = $outputPrinter;
        $this->traitSpotter = $traitSpotter;
    }
    /**
     * @param string[] $sources Paths to scan for traits
     * @param int $maxUsed Maximum number of times a trait is used to be considered lazy
     * @return ExitCode::*
     */
    public function run(array $sources, int $maxUsed = 2) : int
    {
        $this->outputPrinter->title('Looking for trait definitions');
        $traitSpottingResult = $this->traitSpotter->analyse($sources);
        if ($traitSpottingResult->getTraitCount() === 0) {
            $this->outputPrinter->success('No traits were found in your project, nothing to worry about');
            return ExitCode::SUCCESS;
        }
        $this->outputPrinter->writeln(\sprintf('Found %d trait%s in the whole project', $traitSpottingResult->getTraitCount(), $traitSpottingResult->getTraitCount() === 1 ? '' : 's'));
        $this->outputPrinter->listing($traitSpottingResult->getTraitFilePaths());
        $this->outputPrinter->newline();
        $this->outputPrinter->title(\sprintf('Looking for traits used less than %d-times', $maxUsed));
        $leastUsedTraitsMetadatas = $traitSpottingResult->getTraitMaximumUsedTimes($maxUsed);
        foreach ($leastUsedTraitsMetadatas as $leastUsedTraitMetadata) {
            $this->outputPrinter->writeln(\sprintf('Trait "%s" (%d lines) is used only in %d file%s', $leastUsedTraitMetadata->getShortTraitName(), $leastUsedTraitMetadata->getLineCount(), $leastUsedTraitMetadata->getUsedInCount(), $leastUsedTraitMetadata->getUsedInCount() === 1 ? '' : 's'));
            $this->outputPrinter->listing($leastUsedTraitMetadata->getUsedIn());
            $this->outputPrinter->newline();
        }
        $this->outputPrinter->warning(\sprintf('Inline these traits or refactor them to a service if meaningful.%sChange "--max-used" to different number to get more result', \PHP_EOL));
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'spot-lazy-traits';
    }
    public function getDescription() : string
    {
        return 'Spot traits that are use only once, to potentially inline them and make code more robust and readable';
    }
}
