<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Testing\Command;

use SwissKnife202605\Entropy\Console\Contract\CommandInterface;
use SwissKnife202605\Entropy\Console\Enum\ExitCode;
use SwissKnife202605\Nette\Utils\FileSystem;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;
use SwissKnife202605\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202605\Webmozart\Assert\Assert;
final class DetectUnitTestsCommand implements CommandInterface
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
    }
    /**
     * @param string[] $sources Path to directory with tests
     */
    public function run(array $sources) : int
    {
        Assert::allString($sources);
        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);
        if ($unitTestCasesClassesToFilePaths === []) {
            $this->symfonyStyle->note('No unit tests found in provided paths');
            return ExitCode::SUCCESS;
        }
        $filesPHPUnitXmlContents = $this->phpunitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths);
        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents, null);
        $successMessage = \sprintf('List of %d unit tests was dumped into "%s"', \count($unitTestCasesClassesToFilePaths), self::OUTPUT_FILENAME);
        $this->symfonyStyle->success($successMessage);
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'detect-unit-tests';
    }
    public function getDescription() : string
    {
        return 'Get list of tests in specific directory, that are considered "unit"';
    }
}
