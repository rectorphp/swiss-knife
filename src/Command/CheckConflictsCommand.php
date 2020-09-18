<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Command;

use Migrify\EasyCI\Git\ConflictResolver;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class CheckConflictsCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ConflictResolver
     */
    private $conflictResolver;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ConflictResolver $conflictResolver,
        SmartFinder $smartFinder
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->conflictResolver = $conflictResolver;
        $this->smartFinder = $smartFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
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
