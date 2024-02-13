<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202402\Nette\Utils\FileSystem;
use SwissKnife202402\Nette\Utils\Strings;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use SwissKnife202402\Symfony\Component\Console\Command\Command;
use SwissKnife202402\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202402\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202402\Symfony\Component\Console\Input\InputOption;
use SwissKnife202402\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202402\Symfony\Component\Console\Style\SymfonyStyle;
final class FinalizeClassesCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\ParentClassResolver
     */
    private $parentClassResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\EntityClassResolver
     */
    private $entityClassResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     */
    public const NEWLINE_CLASS_START_REGEX = '#^class\\s#m';
    public function __construct(SymfonyStyle $symfonyStyle, ParentClassResolver $parentClassResolver, EntityClassResolver $entityClassResolver, CachedPhpParser $cachedPhpParser)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->parentClassResolver = $parentClassResolver;
        $this->entityClassResolver = $entityClassResolver;
        $this->cachedPhpParser = $cachedPhpParser;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('finalize-classes');
        $this->setDescription('Finalize classes without children');
        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories to finalize');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do no change anything, only list classes about to be finalized');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $paths = (array) $input->getArgument('paths');
        $isDryRun = (bool) $input->getOption('dry-run');
        $this->symfonyStyle->title('1. Detecting parent and entity classes');
        $phpFileInfos = PhpFilesFinder::findPhpFileInfos($paths);
        // double to count for both parent and entity resolver
        $this->symfonyStyle->progressStart(2 * \count($phpFileInfos));
        $progressClosure = function () : void {
            $this->symfonyStyle->progressAdvance();
        };
        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($phpFileInfos, $progressClosure);
        $this->symfonyStyle->progressFinish();
        $this->symfonyStyle->writeln(\sprintf('Found %d parent and %d entity classes', \count($parentClassNames), \count($entityClassNames)));
        $this->symfonyStyle->newLine(1);
        $this->symfonyStyle->title('2. Finalizing safe classes');
        $excludedClasses = \array_merge($parentClassNames, $entityClassNames);
        $needsFinalizeAnalyzer = new NeedsFinalizeAnalyzer($excludedClasses, $this->cachedPhpParser);
        $finalizedFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            // should be file be finalize, is not and is not excluded?
            if (!$needsFinalizeAnalyzer->isNeeded($phpFileInfo->getRealPath())) {
                continue;
            }
            $this->symfonyStyle->writeln(\sprintf('File "%s" %s finalized', $phpFileInfo->getRelativePathname(), $isDryRun ? 'would be' : 'was'));
            $finalizedContents = Strings::replace($phpFileInfo->getContents(), self::NEWLINE_CLASS_START_REGEX, 'final class ');
            $finalizedFilePaths[] = $phpFileInfo->getRelativePath();
            if ($isDryRun === \false) {
                FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents);
            }
        }
        if ($finalizedFilePaths === []) {
            $this->symfonyStyle->success('Nothing to finalize');
            return self::SUCCESS;
        }
        $this->symfonyStyle->success(\sprintf('%d classes %s finalized', \count($finalizedFilePaths), $isDryRun ? 'would be' : 'were'));
        return Command::SUCCESS;
    }
}
