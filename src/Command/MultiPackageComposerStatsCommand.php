<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Composer\ComposerJsonResolver;
use Rector\SwissKnife\Sorting\ArrayFilter;
use Rector\SwissKnife\ValueObject\ComposerJson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class MultiPackageComposerStatsCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ComposerJsonResolver $composerJsonResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('multi-package-composer-stats');

        $this->addArgument(
            'repositories',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more repositories to compare package versions of'
        );

        $this->setDescription(
            'Compares package versions in multiple repositories, to easily sync multiple package upgrade'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repositories = (array) $input->getArgument('repositories');
        Assert::allString($repositories);

        $this->symfonyStyle->note(sprintf(
            'Downloading "composer.json" files for %d repositories',
            count($repositories)
        ));

        $projectsComposerJsons = $this->composerJsonResolver->resolveFromRepositories($repositories);
        $tableHeadlines = $this->resolveTableHeadlines($projectsComposerJsons);

        $requiredPackageNames = $this->resolveProjectsRequiredPackageNames($projectsComposerJsons);
        $tableRows = [];

        foreach ($requiredPackageNames as $requiredPackageName) {
            $tableRow = [$requiredPackageName];

            foreach ($projectsComposerJsons as $composerJson) {
                $tableRow[] = $composerJson->getPackageVersion($requiredPackageName);
            }

            $tableRows[] = $tableRow;
        }

        // sort and put those with both values first
        $tableRows = ArrayFilter::filterOnlyAtLeast2($tableRows);

        $this->symfonyStyle->table($tableHeadlines, $tableRows);

        return self::SUCCESS;
    }

    /**
     * @param ComposerJson[] $projectsComposerJsons
     * @return string[]
     */
    private function resolveTableHeadlines(array $projectsComposerJsons): array
    {
        $tableHeadlines = ['required dependency'];

        foreach ($projectsComposerJsons as $composerJson) {
            $tableHeadlines[] = $composerJson->getRepositoryName();
        }

        return $tableHeadlines;
    }

    /**
     * @param ComposerJson[] $projectsComposerJsons
     * @return string[]
     */
    private function resolveProjectsRequiredPackageNames(array $projectsComposerJsons): array
    {
        $requiredPackageNames = [];
        foreach ($projectsComposerJsons as $composerJson) {
            $requiredPackageNames = array_merge($requiredPackageNames, $composerJson->getRequiredPackageNames());
        }

        return array_unique($requiredPackageNames);
    }
}
