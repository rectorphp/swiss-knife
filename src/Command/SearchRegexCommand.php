<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202504\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use SwissKnife202504\Symfony\Component\Console\Command\Command;
use SwissKnife202504\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202504\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202504\Symfony\Component\Console\Input\InputOption;
use SwissKnife202504\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202504\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202504\Webmozart\Assert\Assert;
final class SearchRegexCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('search-regex');
        $this->addArgument('regex', InputArgument::REQUIRED, 'Code snippet to look in PHP files in the whole codebase');
        $this->addOption('project-directory', null, InputOption::VALUE_REQUIRED, 'Project directory', \getcwd());
        $this->setDescription('Search for regex in PHP files of the whole codebase');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $regex = (string) $input->getArgument('regex');
        $projectDirectory = (string) $input->getOption('project-directory');
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
        return self::SUCCESS;
    }
}
