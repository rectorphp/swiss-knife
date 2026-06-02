<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Git\ConflictResolver;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckConflictsCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Git\ConflictResolver
     */
    private $conflictResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(ConflictResolver $conflictResolver, SymfonyStyle $symfonyStyle)
    {
        $this->conflictResolver = $conflictResolver;
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $sources One or more path to project
     * @return ExitCode::*
     */
    public function run(array $sources) : int
    {
        $fileInfos = FilesFinder::find($sources);
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }
        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($filePaths);
        if ($conflictsCountByFilePath === []) {
            $message = \sprintf('No conflicts found in %d files', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return ExitCode::SUCCESS;
        }
        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = \sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
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
