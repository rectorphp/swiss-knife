<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202605\Entropy\Console\Contract\CommandInterface;
use SwissKnife202605\Entropy\Console\Enum\ExitCode;
use SwissKnife202605\Nette\Utils\FileSystem;
use SwissKnife202605\Nette\Utils\Json;
use Rector\SwissKnife\FileSystem\JsonAnalyzer;
use Rector\SwissKnife\Finder\FilesFinder;
use SwissKnife202605\Symfony\Component\Console\Style\SymfonyStyle;
final class PrettyJsonCommand implements CommandInterface
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
    }
    /**
     * @param string[] $sources JSON file or directory with JSON files to prettify
     * @param bool $dryRun Dry run - no changes will be made
     *
     * @return ExitCode::*
     */
    public function run(array $sources, bool $dryRun = \false) : int
    {
        $jsonFileInfos = FilesFinder::findJsonFiles($sources);
        if ($jsonFileInfos === []) {
            $this->symfonyStyle->error('No *.json files found');
            return ExitCode::ERROR;
        }
        $message = \sprintf('Analysing %d *.json files', \count($jsonFileInfos));
        $this->symfonyStyle->note($message);
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
            if ($dryRun) {
                continue;
            }
            $prettyJsonContent = Json::encode(Json::decode($jsonContent), \JSON_PRETTY_PRINT);
            FileSystem::write($jsonFileInfo->getRealPath(), $prettyJsonContent, null);
        }
        $successMessage = \sprintf('%d file%s %s', \count($printedFilePaths), \count($printedFilePaths) === 1 ? '' : 's', $dryRun ? 'would be changed' : 'changed');
        $this->symfonyStyle->success($successMessage);
        $this->symfonyStyle->listing($printedFilePaths);
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'pretty-json';
    }
    public function getDescription() : string
    {
        return 'Turns JSON files from 1-line to pretty print format';
    }
}
