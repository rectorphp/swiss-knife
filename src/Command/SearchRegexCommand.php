<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202605\Entropy\Console\Contract\CommandInterface;
use SwissKnife202605\Entropy\Console\Enum\ExitCode;
use SwissKnife202605\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use SwissKnife202605\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202605\Webmozart\Assert\Assert;
final class SearchRegexCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string $regex Code snippet to look in PHP files in the whole codebase
     * @param string $projectDirectory Project directory
     *
     * @return ExitCode::*
     */
    public function run(string $regex, ?string $projectDirectory = null) : int
    {
        if ($projectDirectory === null) {
            $projectDirectory = \getcwd();
        }
        Assert::directory($projectDirectory);
        $phpFileInfos = PhpFilesFinder::find([$projectDirectory]);
        $message = \sprintf('Going through %d *.php files', \count($phpFileInfos));
        $this->symfonyStyle->writeln($message);
        $this->symfonyStyle->writeln('Searching for regex: ' . $regex);
        $this->symfonyStyle->newLine();
        $foundCasesCount = 0;
        $markedFiles = [];
        $progressBar = $this->symfonyStyle->createProgressBar(\count($phpFileInfos));
        foreach ($phpFileInfos as $phpFileInfo) {
            $matches = Strings::matchAll($phpFileInfo->getContents(), $regex);
            $currentMatchesCount = \count($matches);
            if ($currentMatchesCount === 0) {
                continue;
            }
            $foundCasesCount += $currentMatchesCount;
            $markedFiles[$phpFileInfo->getRelativePathname()] = $currentMatchesCount;
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->symfonyStyle->newLine(2);
        \ksort($markedFiles);
        foreach ($markedFiles as $filePath => $count) {
            $this->symfonyStyle->writeln(\sprintf(' * %s: %d', $filePath, $count));
        }
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success(\sprintf('Found %d cases in %d files', $foundCasesCount, \count($markedFiles)));
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'search-regex';
    }
    public function getDescription() : string
    {
        return 'Search for regex in PHP files of the whole codebase';
    }
}
