<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FindMultiClassesCommand extends Command
{
    public function __construct(
        private readonly MultipleClassInOneFileFinder $multipleClassInOneFileFinder,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('find-multi-classes');

        $this->setDescription('Find multiple classes in one file');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to source to analyse'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = $input->getArgument('sources');

        $phpFileInfos = PhpFilesFinder::find($source);

        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success(sprintf('No file with 2+ classes found in %d files', count($phpFileInfos)));

            return self::SUCCESS;
        }

        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = PathHelper::relativeToCwd($filePath);

            $message = sprintf('File "%s" contains %d classes', $relativeFilePath, count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }

        return self::FAILURE;
    }
}
