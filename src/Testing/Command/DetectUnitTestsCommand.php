<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Testing\Command;

use SwissKnife202407\Nette\Utils\FileSystem;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;
use SwissKnife202407\Symfony\Component\Console\Command\Command;
use SwissKnife202407\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202407\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202407\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202407\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202407\Webmozart\Assert\Assert;
final class DetectUnitTestsCommand extends Command
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter
     */
    private $phpunitXmlPrinter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Testing\UnitTestFilePathsFinder
     */
    private $unitTestFilePathsFinder;
    /**
     * @var string
     */
    private const OUTPUT_FILENAME = 'phpunit-unit-files.xml';
    public function __construct(PHPUnitXmlPrinter $phpunitXmlPrinter, SymfonyStyle $symfonyStyle, UnitTestFilePathsFinder $unitTestFilePathsFinder)
    {
        $this->phpunitXmlPrinter = $phpunitXmlPrinter;
        $this->symfonyStyle = $symfonyStyle;
        $this->unitTestFilePathsFinder = $unitTestFilePathsFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('detect-unit-tests');
        $this->setDescription('Get list of tests in specific directory, that are considered "unit"');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directory with tests');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        Assert::isArray($sources);
        Assert::allString($sources);
        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);
        if ($unitTestCasesClassesToFilePaths === []) {
            $this->symfonyStyle->note('No unit tests found in provided paths');
            return self::SUCCESS;
        }
        $filesPHPUnitXmlContents = $this->phpunitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths);
        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents);
        $successMessage = \sprintf('List of %d unit tests was dumped into "%s"', \count($unitTestCasesClassesToFilePaths), self::OUTPUT_FILENAME);
        $this->symfonyStyle->success($successMessage);
        return self::SUCCESS;
    }
}
