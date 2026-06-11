<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Nette\Utils\Strings;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Webmozart\Assert\Assert;

final readonly class SearchRegexCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ProgressBar $progressBar,
    ) {
    }

    /**
     * @param string $regex Code snippet to look in PHP files in the whole codebase
     * @param string $projectDirectory Project directory
     *
     * @return ExitCode::*
     */
    public function run(string $regex, ?string $projectDirectory = null): int
    {
        if ($projectDirectory === null) {
            $projectDirectory = getcwd();
        }

        Assert::directory($projectDirectory);

        $phpFileInfos = PhpFilesFinder::find([$projectDirectory]);

        $message = sprintf('Going through %d *.php files', count($phpFileInfos));
        $this->outputPrinter->writeln($message);

        $this->outputPrinter->writeln('Searching for regex: ' . $regex);
        $this->outputPrinter->newline();

        $foundCasesCount = 0;
        $markedFiles = [];

        $this->progressBar->start(count($phpFileInfos));

        foreach ($phpFileInfos as $phpFileInfo) {
            $matches = Strings::matchAll($phpFileInfo->getContents(), $regex);
            $currentMatchesCount = count($matches);
            if ($currentMatchesCount === 0) {
                continue;
            }

            $foundCasesCount += $currentMatchesCount;
            $markedFiles[$phpFileInfo->getRelativePathname()] = $currentMatchesCount;

            $this->progressBar->advance();
        }

        $this->progressBar->finish();
        $this->outputPrinter->newline(2);

        ksort($markedFiles);
        foreach ($markedFiles as $filePath => $count) {
            $this->outputPrinter->writeln(sprintf(' * %s: %d', $filePath, $count));
        }

        $this->outputPrinter->newline(2);
        $this->outputPrinter->success(sprintf('Found %d cases in %d files', $foundCasesCount, count($markedFiles)));

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'search-regex';
    }

    public function getDescription(): string
    {
        return 'Search for regex in PHP files of the whole codebase';
    }
}
