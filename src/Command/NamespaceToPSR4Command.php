<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202409\Nette\Utils\FileSystem;
use SwissKnife202409\Nette\Utils\Strings;
use SwissKnife202409\Symfony\Component\Console\Command\Command;
use SwissKnife202409\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202409\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202409\Symfony\Component\Console\Input\InputOption;
use SwissKnife202409\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202409\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202409\Symfony\Component\Finder\Finder;
use SwissKnife202409\Symfony\Component\Finder\SplFileInfo;
final class NamespaceToPSR4Command extends Command
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
        $this->setName('namespace-to-psr-4');
        $this->setDescription('Change namespace in your PHP files to match PSR-4 root');
        $this->addArgument('path', InputArgument::REQUIRED, 'Single directory path to ensure namespace matches, e.g. "tests"');
        $this->addOption('namespace-root', null, InputOption::VALUE_REQUIRED, 'Namespace root for files in provided path, e.g. "App\\Tests"');
    }
    /**
     * @return self::*
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $path = (string) $input->getArgument('path');
        $namespaceRoot = (string) $input->getOption('namespace-root');
        $fileInfos = $this->findFilesInPath($path);
        $changedFilesCount = 0;
        /** @var SplFileInfo $fileInfo */
        foreach ($fileInfos as $fileInfo) {
            $expectedNamespace = $this->resolveExpectedNamespace($namespaceRoot, $fileInfo);
            $expectedNamespaceLine = 'namespace ' . $expectedNamespace . ';';
            // 1. got the correct namespace
            if (\strpos($fileInfo->getContents(), $expectedNamespaceLine) !== \false) {
                continue;
            }
            // 2. incorrect namespace found
            $this->symfonyStyle->note(\sprintf('File "%s"%s fixed to expected namespace "%s"', $fileInfo->getRelativePathname(), \PHP_EOL, $expectedNamespace));
            // 3. replace
            $correctedContents = Strings::replace($fileInfo->getContents(), '#namespace (.*?);#', $expectedNamespaceLine);
            // 4. print file
            FileSystem::write($fileInfo->getRealPath(), $correctedContents);
            ++$changedFilesCount;
        }
        if ($changedFilesCount === 0) {
            $this->symfonyStyle->success(\sprintf('All %d files have correct namespace', \count($fileInfos)));
        } else {
            $this->symfonyStyle->success(\sprintf('Fixed %d files', $changedFilesCount));
        }
        return self::SUCCESS;
    }
    /**
     * @return SplFileInfo[]
     */
    private function findFilesInPath(string $path) : array
    {
        $finder = Finder::create()->files()->in([$path])->name('*.php')->sortByName()->filter(static function (SplFileInfo $fileInfo) : bool {
            return \strpos($fileInfo->getContents(), 'class ') !== \false;
        });
        return \iterator_to_array($finder->getIterator());
    }
    private function resolveExpectedNamespace(string $namespaceRoot, SplFileInfo $fileInfo) : string
    {
        $relativePathNamespace = \str_replace('/', '\\', $fileInfo->getRelativePath());
        return $namespaceRoot . '\\' . $relativePathNamespace;
    }
}
