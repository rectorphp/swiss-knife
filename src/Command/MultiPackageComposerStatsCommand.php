<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\Strings;
use Rector\SwissKnife\Composer\ComposerJsonResolver;
use Rector\SwissKnife\Sorting\ArrayFilter;
use Rector\SwissKnife\ValueObject\ComposerJson;
use Rector\SwissKnife\ValueObject\ComposerJsonCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
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

        $projectsComposerJsons = $this->composerJsonResolver->resolveFromRepositories($repositories);
        $composerJsonCollection = new ComposerJsonCollection($projectsComposerJsons);

        $tableHeadlines = array_merge(['dependency'], $composerJsonCollection->getRepositoryNames());
        $requiredPackageNames = $composerJsonCollection->getRequiredPackageNames();

        $tableRows = $this->createTableRows($requiredPackageNames, $projectsComposerJsons);

        $table = $this->symfonyStyle->createTable()
            ->setHeaders($tableHeadlines)
            ->setRows($tableRows);

        for ($i = 1; $i < count($tableHeadlines); $i++) {
            $table->setColumnStyle($i, (new TableStyle())->setPadType(STR_PAD_LEFT));
        }

        $table->render();

        $this->symfonyStyle->newLine();

        return self::SUCCESS;
    }

    private function createRedCell(string $content): TableCell
    {
        $redTableCellStyle = new TableCellStyle([
            'bg' => 'red',
            'fg' => 'white',
        ]);

        return new TableCell($content, [
            'style' => $redTableCellStyle,
        ]);
    }

    /**
     * @param string[] $requiredPackageNames
     * @param ComposerJson[] $projectsComposerJsons
     * @return array<mixed[]>
     */
    private function createTableRows(array $requiredPackageNames, array $projectsComposerJsons): array
    {
        $tableRows = [];

        foreach ($requiredPackageNames as $requiredPackageName) {
            $shortRequiredPackageName = Strings::truncate($requiredPackageName, 22);
            $tableRow = [$shortRequiredPackageName];

            foreach ($projectsComposerJsons as $composerJson) {
                $packageVersion = $composerJson->getPackageVersion($requiredPackageName);

                // special case for PHP
                if ($requiredPackageName === 'php' && $packageVersion === null) {
                    $tableRow[] = $this->createRedCell(self::MISSING_LABEL);
                } else {
                    $tableRow[] = $packageVersion;
                }
            }

            $tableRows[] = $tableRow;
        }

        // sort and put those with both values first
        return ArrayFilter::filterOnlyAtLeast2($tableRows);
    }
}
