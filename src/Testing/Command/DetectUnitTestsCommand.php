<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final readonly class DetectUnitTestsCommand implements CommandInterface
{
    private const string OUTPUT_FILENAME = 'phpunit-unit-files.xml';

    public function __construct(
        private PHPUnitXmlPrinter $phpunitXmlPrinter,
        private SymfonyStyle $symfonyStyle,
        private UnitTestFilePathsFinder $unitTestFilePathsFinder,
    ) {
    }

    /**
     * @param string[] $sources Path to directory with tests
     */
    public function run(array $sources): int
    {
        Assert::allString($sources);

        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);

        if ($unitTestCasesClassesToFilePaths === []) {
            $this->symfonyStyle->note('No unit tests found in provided paths');

            return ExitCode::SUCCESS;
        }

        $filesPHPUnitXmlContents = $this->phpunitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths);

        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents, null);

        $successMessage = sprintf(
            'List of %d unit tests was dumped into "%s"',
            count($unitTestCasesClassesToFilePaths),
            self::OUTPUT_FILENAME,
        );

        $this->symfonyStyle->success($successMessage);

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
