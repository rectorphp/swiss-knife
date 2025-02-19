<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Traits\TraitSpotter;
use SwissKnife202502\Symfony\Component\Console\Command\Command;
use SwissKnife202502\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202502\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202502\Symfony\Component\Console\Input\InputOption;
use SwissKnife202502\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202502\Symfony\Component\Console\Style\SymfonyStyle;
final class SpotLazyTraitsCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Traits\TraitSpotter
     */
    private $traitSpotter;
    public function __construct(SymfonyStyle $symfonyStyle, TraitSpotter $traitSpotter)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->traitSpotter = $traitSpotter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('spot-lazy-traits');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check');
        $this->addOption('max-used', null, InputOption::VALUE_REQUIRED, 'Maximum count the trait is used', 2);
        $this->setDescription('Spot traits that are use only once, to potentially inline them and make code more robust and readable');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $maxUsedCount = (int) $input->getOption('max-used');
        $this->symfonyStyle->title('Looking for trait definitions');
        $traitSpottingResult = $this->traitSpotter->analyse($sources);
        if ($traitSpottingResult->getTraitCount() === 0) {
            $this->symfonyStyle->success('No traits were found in your project, nothing to worry about');
            return self::SUCCESS;
        }
        $this->symfonyStyle->writeln(\sprintf('Found %d trait%s in the whole project', $traitSpottingResult->getTraitCount(), $traitSpottingResult->getTraitCount() === 1 ? '' : 's'));
        $this->symfonyStyle->listing($traitSpottingResult->getTraitFilePaths());
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->title(\sprintf('Looking for traits used less than %d-times', $maxUsedCount));
        $leastUsedTraitsMetadatas = $traitSpottingResult->getTraitMaximumUsedTimes($maxUsedCount);
        foreach ($leastUsedTraitsMetadatas as $leastUsedTraitMetadata) {
            $this->symfonyStyle->writeln(\sprintf('Trait "%s" (%d lines) is used only in %d file%s', $leastUsedTraitMetadata->getShortTraitName(), $leastUsedTraitMetadata->getLineCount(), $leastUsedTraitMetadata->getUsedInCount(), $leastUsedTraitMetadata->getUsedInCount() === 1 ? '' : 's'));
            $this->symfonyStyle->listing($leastUsedTraitMetadata->getUsedIn());
            $this->symfonyStyle->newLine();
        }
        $this->symfonyStyle->warning(\sprintf('Inline these traits or refactor them to a service if meaningful.%sChange "--max-used" to different number to get more result', \PHP_EOL));
        return self::SUCCESS;
    }
}
