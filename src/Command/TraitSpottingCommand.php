<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Traits\TraitSpotter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TraitSpottingCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly TraitSpotter $traitSpotter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('trait-spotting');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check'
        );

        $this->addOption('max-used', null, InputOption::VALUE_REQUIRED, 'Maximum count the trait is used', 2);

        $this->setDescription(
            'Spot traits that are use only once, to potentially inline them and make code more robust and readable'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $maxUsedCount = (int) $input->getArgument('max-used');

        $this->symfonyStyle->title('Analysing single-used traits, shorter first');
        $traitSpottingResult = $this->traitSpotter->analyse($sources);

        $this->symfonyStyle->note(sprintf('Found %d traits', $traitSpottingResult->getTraitCount()));

        $maxTimesUsedTraits = $traitSpottingResult->getTraitMaximumUsedTimes($maxUsedCount);
        $this->symfonyStyle->section(sprintf('Found %d traits', count($maxTimesUsedTraits)));

        foreach ($maxTimesUsedTraits as $maxTimesUsedTrait) {

        }

        return self::SUCCESS;
    }
}
