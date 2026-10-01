<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Utils\FileSystem;
use Entropy\Validation\Assert;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;

final class DetectUnitTestsCommand implements CommandInterface
{
    /**
     * @readonly
     */
    private PHPUnitXmlPrinter $phpUnitXmlPrinter;

    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    /**
     * @readonly
     */
    private UnitTestFilePathsFinder $unitTestFilePathsFinder;

    /**
     * @var string
     */
    private const OUTPUT_FILENAME = 'phpunit-unit-files.xml';

    public function __construct(PHPUnitXmlPrinter $phpunitXmlPrinter, OutputPrinter $outputPrinter, UnitTestFilePathsFinder $unitTestFilePathsFinder)
    {
        $this->phpUnitXmlPrinter = $phpunitXmlPrinter;
        $this->outputPrinter = $outputPrinter;
        $this->unitTestFilePathsFinder = $unitTestFilePathsFinder;
    }

    /**
     * @param string[] $sources Path to directory with tests
     */
    public function run(array $sources): int
    {
        Assert::allString($sources);

        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);

        if ($unitTestCasesClassesToFilePaths === []) {
            $this->outputPrinter->yellow('No unit tests found in provided paths');

            return ExitCode::SUCCESS;
        }

        $filesPHPUnitXmlContents = $this->phpUnitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths);

        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents);

        $successMessage = sprintf(
            'List of %d unit tests was dumped into "%s"',
            count($unitTestCasesClassesToFilePaths),
            self::OUTPUT_FILENAME,
        );

        $this->outputPrinter->success($successMessage);

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'detect-unit-tests';
    }

    public function getDescription(): string
    {
        return 'Get list of tests in specific directory, that are considered "unit"';
    }
}
