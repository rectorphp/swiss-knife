<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Config\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\ValueObject\ConfigFileSuffixes;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI202301\Symplify\PackageBuilder\ValueObject\Option;
final class CheckConfigCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor
     */
    private $classAndConstantExistanceFileProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(ClassAndConstantExistanceFileProcessor $classAndConstantExistanceFileProcessor, FileErrorsReporter $fileErrorsReporter)
    {
        $this->classAndConstantExistanceFileProcessor = $classAndConstantExistanceFileProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-config');
        $this->setDescription('Check YAML configs for existing classes and class constants');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directories or files to check');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, ConfigFileSuffixes::provideRegex(), ['Fixture']);
        $message = \sprintf('Checking %d files with "%s" suffixes', \count($fileInfos), \implode('", "', ConfigFileSuffixes::SUFFIXES));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->classAndConstantExistanceFileProcessor->processFileInfos($fileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}
