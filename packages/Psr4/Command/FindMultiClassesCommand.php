<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\Finder\MultipleClassInOneFileFinder;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class FindMultiClassesCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Psr4\Finder\MultipleClassInOneFileFinder
     */
    private $multipleClassInOneFileFinder;
    public function __construct(MultipleClassInOneFileFinder $multipleClassInOneFileFinder)
    {
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('find-multi-classes');
        $this->setDescription('Find multiple classes in one file');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source to analyse');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCES);
        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success('No files with 2+ classes found');
            return self::SUCCESS;
        }
        foreach ($multipleClassesByFile as $file => $classes) {
            $message = \sprintf('File "%s" has %d classes', $file, \count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }
        return self::FAILURE;
    }
}
