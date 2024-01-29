<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Finder\MultipleClassInOneFileFinder;

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

        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success('No files with 2+ classes found');

            return self::SUCCESS;
        }

        foreach ($multipleClassesByFile as $file => $classes) {
            $message = sprintf('File "%s" has %d classes', $file, count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }

        return self::FAILURE;
    }
}
