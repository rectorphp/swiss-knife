<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Git\ConflictResolver;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckConflictsCommand implements \Entropy\Console\Contract\CommandInterface
{
    public function __construct(
        private readonly ConflictResolver $conflictResolver,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    /**
     * @param string[] $sources One or more path to project
     * @return ExitCode::*
     */
    public function run(array $sources): int
    {
        $fileInfos = FilesFinder::find($sources);

        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }

        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($filePaths);
        if ($conflictsCountByFilePath === []) {
            $message = sprintf('No conflicts found in %d files', count($fileInfos));
            $this->symfonyStyle->success($message);

            return \Entropy\Console\Enum\ExitCode::SUCCESS;
        }

        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }

        return \Entropy\Console\Enum\ExitCode::ERROR;
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
