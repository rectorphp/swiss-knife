<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Testing\Command;

use EasyCI20220125\Symfony\Component\Console\Command\Command;
use EasyCI20220125\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220125\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220125\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220125\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Testing\Printer\PHPUnitXmlPrinter;
use Symplify\EasyCI\Testing\UnitTestFilePathsFinder;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220125\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220125\Symplify\SmartFileSystem\SmartFileSystem;
use EasyCI20220125\Webmozart\Assert\Assert;
final class DetectUnitTestsCommand extends \EasyCI20220125\Symfony\Component\Console\Command\Command
{
    /**
     * @var string
     */
    private const OUTPUT_FILENAME = 'phpunit-unit-files.xml';
    /**
     * @var \Symplify\EasyCI\Testing\Printer\PHPUnitXmlPrinter
     */
    private $phpunitXmlPrinter;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\EasyCI\Testing\UnitTestFilePathsFinder
     */
    private $unitTestFilePathsFinder;
    public function __construct(\Symplify\EasyCI\Testing\Printer\PHPUnitXmlPrinter $phpunitXmlPrinter, \EasyCI20220125\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \EasyCI20220125\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \Symplify\EasyCI\Testing\UnitTestFilePathsFinder $unitTestFilePathsFinder)
    {
        $this->phpunitXmlPrinter = $phpunitXmlPrinter;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->unitTestFilePathsFinder = $unitTestFilePathsFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220125\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Get list of tests in specific directory, that are considered "unit".
They depend only on bare PHPUnit test case, but not on KernelTestCase. Move the generated file to your phpunit.xml test group.');
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220125\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220125\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to directory with tests');
    }
    protected function execute(\EasyCI20220125\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220125\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        \EasyCI20220125\Webmozart\Assert\Assert::isArray($sources);
        \EasyCI20220125\Webmozart\Assert\Assert::allString($sources);
        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);
        if ($unitTestCasesClassesToFilePaths === []) {
            $this->symfonyStyle->note('No unit tests found in provided paths');
            return self::SUCCESS;
        }
        $filesPHPUnitXmlContents = $this->phpunitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths, \getcwd());
        $this->smartFileSystem->dumpFile(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents);
        $successMessage = \sprintf('List of %d unit tests was dumped into "%s"', \count($unitTestCasesClassesToFilePaths), self::OUTPUT_FILENAME);
        $this->symfonyStyle->success($successMessage);
        return self::SUCCESS;
    }
}
