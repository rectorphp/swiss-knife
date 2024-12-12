<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Git\ConflictResolver;
use SwissKnife202412\Symfony\Component\Console\Command\Command;
use SwissKnife202412\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202412\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202412\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202412\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckConflictsCommand extends Command
{
    /**
     * @readonly
     */
    private ConflictResolver $conflictResolver;
    /**
     * @readonly
     */
    private SymfonyStyle $symfonyStyle;
    public function __construct(ConflictResolver $conflictResolver, SymfonyStyle $symfonyStyle)
    {
        $this->conflictResolver = $conflictResolver;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-conflicts');
        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument('sources');
        $fileInfos = FilesFinder::find($sources);
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }
        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($filePaths);
        if ($conflictsCountByFilePath === []) {
            $message = \sprintf('No conflicts found in %d files', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
        }
        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = \sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }
        return self::FAILURE;
    }
}
