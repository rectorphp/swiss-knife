<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Git\ConflictResolver;

final readonly class CheckConflictsCommand implements CommandInterface
{
    public function __construct(
        private ConflictResolver $conflictResolver,
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param string[] $sources One or more path to project
     * @param string[] $exclude Skip files or directories by path
     * @return ExitCode::*
     */
    public function run(array $sources, array $exclude = []): int
    {
        $fileInfos = FilesFinder::find($sources, $exclude);

        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }

        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($filePaths);
        if ($conflictsCountByFilePath === []) {
            $message = sprintf('No conflicts found in %d files', count($fileInfos));
            $this->outputPrinter->success($message);

            return ExitCode::SUCCESS;
        }

        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->outputPrinter->error($message);
        }

        return ExitCode::ERROR;
    }

    public function getName(): string
    {
        return 'check-conflicts';
    }

    public function getDescription(): string
    {
        return 'Check files for missed git conflicts';
    }
}
