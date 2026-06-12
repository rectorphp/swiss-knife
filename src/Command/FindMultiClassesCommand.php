<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final readonly class FindMultiClassesCommand implements CommandInterface
{
    public function __construct(
        private MultipleClassInOneFileFinder $multipleClassInOneFileFinder,
        private OutputPrinter $outputPrinter,
    ) {
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
            $this->outputPrinter->success(sprintf('No file with 2+ classes found in %d files', count($phpFileInfos)));

            return ExitCode::SUCCESS;
        }

        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = PathHelper::relativeToCwd($filePath);

            $message = sprintf('File "%s" contains %d classes', $relativeFilePath, count($classes));
            $this->outputPrinter->section($message);
            $this->outputPrinter->listing($classes);
        }

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
