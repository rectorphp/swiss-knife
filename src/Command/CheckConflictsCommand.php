<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Command;

use EasyCI202402\Rector\SwissKnife\Finder\FilesFinder;
use EasyCI202402\Rector\SwissKnife\Git\ConflictResolver;
use EasyCI202402\Symfony\Component\Console\Command\Command;
use EasyCI202402\Symfony\Component\Console\Input\InputArgument;
use EasyCI202402\Symfony\Component\Console\Input\InputInterface;
use EasyCI202402\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckConflictsCommand extends Command
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
