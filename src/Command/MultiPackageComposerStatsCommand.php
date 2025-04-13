<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202504\Nette\Utils\Strings;
use Rector\SwissKnife\Composer\ComposerJsonResolver;
use Rector\SwissKnife\Helper\SymfonyColumnStyler;
use Rector\SwissKnife\ValueObject\ComposerJsonCollection;
use SwissKnife202504\Symfony\Component\Console\Command\Command;
use SwissKnife202504\Symfony\Component\Console\Helper\TableStyle;
use SwissKnife202504\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202504\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202504\Symfony\Component\Console\Input\InputOption;
use SwissKnife202504\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202504\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202504\Webmozart\Assert\Assert;
final class MultiPackageComposerStatsCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Composer\ComposerJsonResolver
     */
    private $composerJsonResolver;
    /**
     * @var string
     */
    private const MISSING_LABEL = '*MISSING*';
    public function __construct(SymfonyStyle $symfonyStyle, ComposerJsonResolver $composerJsonResolver)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonResolver = $composerJsonResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('multi-package-composer-stats');
        $this->addArgument('repositories', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more repositories to compare package versions of');
        $this->addOption('is-source', null, InputOption::VALUE_NONE, 'Provided repositories are main sources, analyze their dependencies instead');
        $this->setDescription('Compares package versions in multiple repositories, to easily sync multiple package upgrade');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $repositories = (array) $input->getArgument('repositories');
        $isSource = (bool) $input->getOption('is-source');
        Assert::allString($repositories);
        if ($isSource) {
            $this->symfonyStyle->title(\sprintf('Loading dependencies for %d projects', \count($repositories)));
            $projectsComposerJson = $this->composerJsonResolver->resolveFromRepositories($repositories);
            foreach ($projectsComposerJson->all() as $i => $projectComposerJson) {
                $this->symfonyStyle->section(\sprintf('%d) Showing external repositories for "%s" project', $i + 1, $projectComposerJson->getRepositoryName()));
                $this->renderTableForRepositories($projectComposerJson->getRequiredRepositories());
                $this->symfonyStyle->newLine();
            }
            return self::SUCCESS;
        }
        $this->symfonyStyle->title(\sprintf('Loading "composer.json" files for %d repositories', \count($repositories)));
        $this->renderTableForRepositories($repositories);
        return self::SUCCESS;
    }
    /**
     * @param string[] $requiredPackageNames
     * @return array<mixed[]>
     */
    private function createTableRows(array $requiredPackageNames, ComposerJsonCollection $composerJsonCollection) : array
    {
        $tableRows = [];
        foreach ($requiredPackageNames as $requiredPackageName) {
            $knownValuesCount = 0;
            $dataRow = [];
            foreach ($composerJsonCollection->all() as $composerJson) {
                $packageVersion = $composerJson->getPackageVersion($requiredPackageName);
                if ($packageVersion !== null) {
                    $knownValuesCount++;
                }
                if ($this->isUnknownPhp($requiredPackageName, $packageVersion)) {
                    $dataRow[] = SymfonyColumnStyler::createRedCell(self::MISSING_LABEL);
                } else {
                    $dataRow[] = $packageVersion;
                }
            }
            // we need at least 2 values to compare
            if ($requiredPackageName !== 'php' && $knownValuesCount < 2) {
                continue;
            }
            $dataRow = SymfonyColumnStyler::styleHighsAndLows($dataRow);
            $shortRequiredPackageName = Strings::truncate($requiredPackageName, 22);
            $tableRow = \array_merge([$shortRequiredPackageName], $dataRow);
            $tableRows[] = $tableRow;
        }
        return $tableRows;
    }
    /**
     * @param string[] $tableHeadlines
     * @param mixed[] $tableRows
     */
    private function renderTable(array $tableHeadlines, array $tableRows) : void
    {
        $table = $this->symfonyStyle->createTable()->setHeaders($tableHeadlines)->setRows($tableRows);
        // align number to right to
        $counter = \count($tableHeadlines);
        // align number to right to
        for ($i = 1; $i < $counter; $i++) {
            $table->setColumnStyle($i, (new TableStyle())->setPadType(\STR_PAD_LEFT));
        }
        $table->render();
        $this->symfonyStyle->newLine();
    }
    private function isUnknownPhp(string $packageName, ?string $packageVersion) : bool
    {
        if ($packageName !== 'php') {
            return \false;
        }
        return $packageVersion === null;
    }
    /**
     * @param string[] $repositories
     */
    private function renderTableForRepositories(array $repositories) : void
    {
        $composerJsonCollection = $this->composerJsonResolver->resolveFromRepositories($repositories);
        $requiredPackageNames = $composerJsonCollection->getRequiredPackageNames();
        $tableHeadlines = \array_merge(['dependency'], $composerJsonCollection->getRepositoryNames());
        $tableRows = $this->createTableRows($requiredPackageNames, $composerJsonCollection);
        $this->renderTable($tableHeadlines, $tableRows);
    }
}
