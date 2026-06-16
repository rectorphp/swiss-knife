<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Git\ConflictResolver;
final class CheckConflictsCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Git\ConflictResolver
     */
    private $conflictResolver;
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(ConflictResolver $conflictResolver, OutputPrinter $outputPrinter)
    {
        $this->conflictResolver = $conflictResolver;
        $this->outputPrinter = $outputPrinter;
    }
    /**
     * @param string[] $sources One or more path to project
     * @param string[] $exclude Skip files or directories by path
     * @return ExitCode::*
     */
    public function run(array $sources, array $exclude = []) : int
    {
        $fileInfos = FilesFinder::find($sources, $exclude);
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }
        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($filePaths);
        if ($conflictsCountByFilePath === []) {
            $message = \sprintf('No conflicts found in %d files', \count($fileInfos));
            $this->outputPrinter->success($message);
            return ExitCode::SUCCESS;
        }
        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = \sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->outputPrinter->error($message);
        }
        return ExitCode::ERROR;
    }
    public function getName() : string
    {
        return 'check-conflicts';
    }
    public function getDescription() : string
    {
        return 'Check files for missed git conflicts';
    }
}
