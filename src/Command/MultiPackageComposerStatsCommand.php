<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\Strings;
use Rector\SwissKnife\Composer\ComposerJsonResolver;
use Rector\SwissKnife\Helper\SymfonyColumnStyler;
use Rector\SwissKnife\Sorting\ArrayFilter;
use Rector\SwissKnife\ValueObject\ComposerJsonCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableStyle;
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
    private const MISSING_LABEL = '*MISSING*';

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

        $this->symfonyStyle->title(sprintf(
            'Loading "composer.json" files for %d repositories',
            count($repositories)
        ));

        $composerJsonCollection = $this->composerJsonResolver->resolveFromRepositories($repositories);

        $tableHeadlines = array_merge(['dependency'], $composerJsonCollection->getRepositoryNames());
        $requiredPackageNames = $composerJsonCollection->getRequiredPackageNames();

        $tableRows = $this->createTableRows($requiredPackageNames, $composerJsonCollection);

        $this->renderTable($tableHeadlines, $tableRows);

        return self::SUCCESS;
    }

    /**
     * @param string[] $requiredPackageNames
     * @return array<mixed[]>
     */
    private function createTableRows(array $requiredPackageNames, ComposerJsonCollection $composerJsonCollection): array
    {
        $tableRows = [];

        foreach ($requiredPackageNames as $requiredPackageName) {
            $shortRequiredPackageName = Strings::truncate($requiredPackageName, 22);
            $tableRow = [$shortRequiredPackageName];

            foreach ($composerJsonCollection->all() as $composerJson) {
                $packageVersion = $composerJson->getPackageVersion($requiredPackageName);

                // special case for PHP
                if ($requiredPackageName === 'php' && $packageVersion === null) {
                    $tableRow[] = SymfonyColumnStyler::createRedCell(self::MISSING_LABEL);
                } else {
                    $tableRow[] = $packageVersion;
                }
            }

            $tableRows[] = $tableRow;
        }

        // sort and put those with both values first
        return ArrayFilter::filterOnlyAtLeast2($tableRows);
    }

    /**
     * @param string[] $tableHeadlines
     * @param mixed[] $tableRows
     */
    private function renderTable(array $tableHeadlines, array $tableRows): void
    {
        $table = $this->symfonyStyle->createTable()
            ->setHeaders($tableHeadlines)
            ->setRows($tableRows);

        // align number to right to
        for ($i = 1; $i < count($tableHeadlines); $i++) {
            $table->setColumnStyle($i, (new TableStyle())->setPadType(STR_PAD_LEFT));
        }

        $table->render();
        $this->symfonyStyle->newLine();
    }
}
