<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Traits\TraitSpotter;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class SpotLazyTraitsCommand implements CommandInterface
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private TraitSpotter $traitSpotter,
    ) {
    }

    /**
     * @param string[] $sources Paths to scan for traits
     * @param int $maxUsed Maximum number of times a trait is used to be considered lazy
     * @return ExitCode::*
     */
    public function run(array $sources, int $maxUsed = 2): int
    {
        $this->symfonyStyle->title('Looking for trait definitions');
        $traitSpottingResult = $this->traitSpotter->analyse($sources);

        if ($traitSpottingResult->getTraitCount() === 0) {
            $this->symfonyStyle->success('No traits were found in your project, nothing to worry about');

            return ExitCode::SUCCESS;
        }

        $this->symfonyStyle->writeln(
            sprintf(
                'Found %d trait%s in the whole project',
                $traitSpottingResult->getTraitCount(),
                $traitSpottingResult->getTraitCount() === 1 ? '' : 's'
            )
        );
        $this->symfonyStyle->listing($traitSpottingResult->getTraitFilePaths());

        $this->symfonyStyle->newLine();

        $this->symfonyStyle->title(sprintf('Looking for traits used less than %d-times', $maxUsed));

        $leastUsedTraitsMetadatas = $traitSpottingResult->getTraitMaximumUsedTimes($maxUsed);

        foreach ($leastUsedTraitsMetadatas as $leastUsedTraitMetadata) {
            $this->symfonyStyle->writeln(sprintf(
                'Trait "%s" (%d lines) is used only in %d file%s',
                $leastUsedTraitMetadata->getShortTraitName(),
                $leastUsedTraitMetadata->getLineCount(),
                $leastUsedTraitMetadata->getUsedInCount(),
                $leastUsedTraitMetadata->getUsedInCount() === 1 ? '' : 's'
            ));

            $this->symfonyStyle->listing($leastUsedTraitMetadata->getUsedIn());
            $this->symfonyStyle->newLine();
        }

        $this->symfonyStyle->warning(sprintf(
            'Inline these traits or refactor them to a service if meaningful.%sChange "--max-used" to different number to get more result',
            PHP_EOL
        ));

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'spot-lazy-traits';
    }

    public function getDescription(): string
    {
        return 'Spot traits that are use only once, to potentially inline them and make code more robust and readable';
    }
}
