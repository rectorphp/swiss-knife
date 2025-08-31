<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing\Command;

use Nette\Utils\FileSystem;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class DetectUnitTestsCommand extends Command
{
    /**
     * @var string
     */
    private const OUTPUT_FILENAME = 'phpunit-unit-files.xml';

    public function __construct(
        private readonly PHPUnitXmlPrinter $phpunitXmlPrinter,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly UnitTestFilePathsFinder $unitTestFilePathsFinder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('detect-unit-tests');

        $this->setDescription('Get list of tests in specific directory, that are considered "unit"');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory with tests'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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

        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents, null);

        $successMessage = sprintf(
            'List of %d unit tests was dumped into "%s"',
            count($unitTestCasesClassesToFilePaths),
            self::OUTPUT_FILENAME,
        );

        $this->symfonyStyle->success($successMessage);

        return self::SUCCESS;
    }
}
