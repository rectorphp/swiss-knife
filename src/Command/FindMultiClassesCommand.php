<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Command;

use EasyCI202402\Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use EasyCI202402\Symfony\Component\Console\Command\Command;
use EasyCI202402\Symfony\Component\Console\Input\InputArgument;
use EasyCI202402\Symfony\Component\Console\Input\InputInterface;
use EasyCI202402\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
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
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $source */
        $source = $input->getArgument('sources');
        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success('No files with 2+ classes found');
            return self::SUCCESS;
        }
        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = \str_replace(\getcwd() . '/', '', $filePath);
            $message = \sprintf('File "%s" contains %d classes', $relativeFilePath, \count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }
        return self::FAILURE;
    }
}
