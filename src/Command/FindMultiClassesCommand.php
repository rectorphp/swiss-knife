<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
final class FindMultiClassesCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Finder\MultipleClassInOneFileFinder
     */
    private $multipleClassInOneFileFinder;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(MultipleClassInOneFileFinder $multipleClassInOneFileFinder, SymfonyStyle $symfonyStyle)
    {
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $sources Path to source to analyse
     * @param string[] $excludePaths Paths to exclude
     *
     * @return ExitCode::*
     */
    public function run(array $sources, array $excludePaths) : int
    {
        $phpFileInfos = PhpFilesFinder::find($sources, $excludePaths);
        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($sources, $excludePaths);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success(\sprintf('No file with 2+ classes found in %d files', \count($phpFileInfos)));
            return ExitCode::SUCCESS;
        }
        foreach ($multipleClassesByFile as $filePath => $classes) {
            // get relative path to getcwd()
            $relativeFilePath = PathHelper::relativeToCwd($filePath);
            $message = \sprintf('File "%s" contains %d classes', $relativeFilePath, \count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }
        return ExitCode::ERROR;
    }
    public function getName() : string
    {
        return 'find-multi-classes';
    }
    public function getDescription() : string
    {
        return 'Find multiple classes in one file';
    }
}
