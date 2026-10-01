<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final class FindMultiClassesCommand implements CommandInterface
{
    /**
     * @readonly
     */
    private MultipleClassInOneFileFinder $multipleClassInOneFileFinder;

    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    public function __construct(MultipleClassInOneFileFinder $multipleClassInOneFileFinder, OutputPrinter $outputPrinter)
    {
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;
        $this->outputPrinter = $outputPrinter;
    }

    /**
     * @param string[] $sources Path to source to analyse
     * @param string[] $excludePaths Paths to exclude
     *
     * @return ExitCode::*
     */
    public function run(array $sources, array $excludePaths): int
    {
        $phpFileInfos = PhpFilesFinder::find($sources, $excludePaths);

        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($sources, $excludePaths);
        if ($multipleClassesByFile === []) {
            $fileCount = count($phpFileInfos);
            $this->outputPrinter->success(sprintf(
                'No file with 2+ classes found in %d file%s',
                $fileCount,
                $fileCount === 1 ? '' : 's'
            ));

            return ExitCode::SUCCESS;
        }

        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = PathHelper::relativeToCwd($filePath);

            $classCount = count($classes);
            $message = sprintf('File "%s" contains %d class%s', $relativeFilePath, $classCount, $classCount === 1 ? '' : 'es');
            $this->outputPrinter->section($message);
            $this->outputPrinter->listing($classes);
        }

        $fileWithMultipleClassesCount = count($multipleClassesByFile);
        $this->outputPrinter->error(sprintf(
            'Found %d file%s with multiple classes, split each into its own file',
            $fileWithMultipleClassesCount,
            $fileWithMultipleClassesCount === 1 ? '' : 's'
        ));

        return ExitCode::ERROR;
    }

    public function getName(): string
    {
        return 'find-multi-classes';
    }

    public function getDescription(): string
    {
        return 'Find multiple classes in one file';
    }
}
