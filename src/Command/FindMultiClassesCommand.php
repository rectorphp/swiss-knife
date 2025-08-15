<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use SwissKnife202508\Symfony\Component\Console\Command\Command;
use SwissKnife202508\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202508\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202508\Symfony\Component\Console\Input\InputOption;
use SwissKnife202508\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202508\Symfony\Component\Console\Style\SymfonyStyle;
final class FindMultiClassesCommand extends Command
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Finder\MultipleClassInOneFileFinder
     */
    private $multipleClassInOneFileFinder;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(MultipleClassInOneFileFinder $multipleClassInOneFileFinder, SymfonyStyle $symfonyStyle)
    {
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('find-multi-classes');
        $this->setDescription('Find multiple classes in one file');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source to analyse');
        $this->addOption('exclude-path', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Path to exclude');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $source */
        $source = $input->getArgument('sources');
        $excludedPaths = (array) $input->getOption('exclude-path');
        $phpFileInfos = PhpFilesFinder::find($source, $excludedPaths);
        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source, $excludedPaths);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success(\sprintf('No file with 2+ classes found in %d files', \count($phpFileInfos)));
            return self::SUCCESS;
        }
        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = PathHelper::relativeToCwd($filePath);
            $message = \sprintf('File "%s" contains %d classes', $relativeFilePath, \count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }
        return self::FAILURE;
    }
}
