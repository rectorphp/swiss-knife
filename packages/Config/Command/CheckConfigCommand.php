<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Config\Command;

use EasyCI20220224\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220224\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220224\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\ValueObject\ConfigFileSuffixes;
use EasyCI20220224\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220224\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220224\Symplify\PackageBuilder\ValueObject\Option;
final class CheckConfigCommand extends \EasyCI20220224\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor
     */
    private $classAndConstantExistanceFileProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(\Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor $classAndConstantExistanceFileProcessor, \Symplify\EasyCI\Console\Output\FileErrorsReporter $fileErrorsReporter)
    {
        $this->classAndConstantExistanceFileProcessor = $classAndConstantExistanceFileProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220224\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Check NEON and YAML configs for existing classes and class constants');
        $this->addArgument(\EasyCI20220224\Symplify\PackageBuilder\ValueObject\Option::SOURCES, \EasyCI20220224\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220224\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to directories or files to check');
    }
    protected function execute(\EasyCI20220224\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220224\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(\EasyCI20220224\Symplify\PackageBuilder\ValueObject\Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, \Symplify\EasyCI\ValueObject\ConfigFileSuffixes::provideRegex(), ['Fixture']);
        $message = \sprintf('Checking %d files with "%s" suffixes', \count($fileInfos), \implode('", "', \Symplify\EasyCI\ValueObject\ConfigFileSuffixes::SUFFIXES));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->classAndConstantExistanceFileProcessor->processFileInfos($fileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}
