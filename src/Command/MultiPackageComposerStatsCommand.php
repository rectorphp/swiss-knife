<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Rector\SwissKnife\Sorting\ArraySorter;
use Rector\SwissKnife\ValueObject\ComposerJson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class MultiPackageComposerStatsCommand extends Command
{
    /**
     * @var string
     */
    private const TEMP_PROJECT_COMPOSER_JSON = 'temp-project-composer.json';

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
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

        $this->symfonyStyle->newLine();

        $projectsComposerJsons = $this->resolveProjectComposerJsons($repositories);
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
        $tableRows = ArraySorter::putSharedFirst($tableRows);
        $this->symfonyStyle->table($tableHeadlines, $tableRows);

        return self::SUCCESS;
    }


    /**
     * @param string[] $repositories
     * @return ComposerJson[]
     */
    private function resolveProjectComposerJsons(array $repositories): array
    {
        Assert::allString($repositories);

        $projectsComposerJsons = [];
        foreach ($repositories as $repository) {
            // clones only "composer.json" file
            exec(sprintf(
                'git archive --remote=%s HEAD composer.json | tar -xO composer.json > %s',
                $repository,
                self::TEMP_PROJECT_COMPOSER_JSON
            ));

            $projectsComposerJsonContents = FileSystem::read(self::TEMP_PROJECT_COMPOSER_JSON);

            Assert::string($projectsComposerJsonContents);
            Assert::notEmpty($projectsComposerJsonContents);

            $projectsComposerJsons[] = new ComposerJson($repository, $projectsComposerJsonContents);
        }

        // tidy up temporary file
        FileSystem::delete(self::TEMP_PROJECT_COMPOSER_JSON);

        return $projectsComposerJsons;
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
