<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Command;

use Migrify\EasyCI\Git\ConflictResolver;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckConflictsCommand extends AbstractMigrifyCommand
{
    /**
     * @var ConflictResolver
     */
    private $conflictResolver;

    public function __construct(ConflictResolver $conflictResolver)
    {
        $this->conflictResolver = $conflictResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to project'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(MigrifyOption::SOURCES);

        $fileInfos = $this->smartFinder->find($source, '*');

        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($fileInfos);
        if ($conflictsCountByFilePath === []) {
            $message = sprintf('No conflicts found in %d files', count($fileInfos));
            $this->symfonyStyle->success($message);

            return ShellCode::SUCCESS;
        }

        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = sprintf('File "%s" contains %d unresolved conflict', $file, $conflictCount);
            $this->symfonyStyle->error($message);
            $this->symfonyStyle->newLine();
        }

        return ShellCode::ERROR;
    }
}
