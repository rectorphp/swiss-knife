<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Finder\FilesFinder;
use Symplify\EasyCI\Git\ConflictResolver;

final class CheckConflictsCommand extends Command
{
    public function __construct(
        private readonly ConflictResolver $conflictResolver,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-conflicts');

        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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
            $message = sprintf('No conflicts found in %d files', count($fileInfos));
            $this->symfonyStyle->success($message);

            return self::SUCCESS;
        }

        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }

        return self::FAILURE;
    }
}
