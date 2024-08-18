<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202408\Nette\Utils\FileSystem;
use SwissKnife202408\Nette\Utils\Json;
use Rector\SwissKnife\FileSystem\JsonAnalyzer;
use Rector\SwissKnife\Finder\FilesFinder;
use SwissKnife202408\Symfony\Component\Console\Command\Command;
use SwissKnife202408\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202408\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202408\Symfony\Component\Console\Input\InputOption;
use SwissKnife202408\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202408\Symfony\Component\Console\Style\SymfonyStyle;
final class PrettyJsonCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\FileSystem\JsonAnalyzer
     */
    private $jsonAnalyzer;
    public function __construct(SymfonyStyle $symfonyStyle, JsonAnalyzer $jsonAnalyzer)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->jsonAnalyzer = $jsonAnalyzer;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('pretty-json');
        $this->setDescription('Turns JSON files from 1-line to pretty print format');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'JSON file or directory with JSON files to prettify');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run - no changes will be made');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $jsonFileInfos = FilesFinder::findJsonFiles($sources);
        if ($jsonFileInfos === []) {
            $this->symfonyStyle->error('No *.json files found');
            return self::FAILURE;
        }
        $message = \sprintf('Analysing %d *.json files', \count($jsonFileInfos));
        $this->symfonyStyle->note($message);
        $isDryRun = (bool) $input->getOption('dry-run');
        $printedFilePaths = [];
        // convert file infos from uggly json to pretty json
        foreach ($jsonFileInfos as $jsonFileInfo) {
            $jsonContent = FileSystem::read($jsonFileInfo->getRealPath());
            if ($this->jsonAnalyzer->isPrettyPrinted($jsonContent)) {
                $this->symfonyStyle->writeln(\sprintf('File "%s" is already pretty', $jsonFileInfo->getRelativePathname()));
                continue;
            }
            // notify the file was changed
            $printedFilePaths[] = $jsonFileInfo->getRelativePathname();
            // nothing will be changed
            if ($isDryRun) {
                continue;
            }
            $prettyJsonContent = Json::encode(Json::decode($jsonContent), \JSON_PRETTY_PRINT);
            FileSystem::write($jsonFileInfo->getRealPath(), $prettyJsonContent);
        }
        $successMessage = \sprintf('%d file%s %s', \count($printedFilePaths), \count($printedFilePaths) === 1 ? '' : 's', $isDryRun ? 'would be changed' : 'changed');
        $this->symfonyStyle->success($successMessage);
        $this->symfonyStyle->listing($printedFilePaths);
        return self::SUCCESS;
    }
}
