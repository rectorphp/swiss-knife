<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202507\Nette\Utils\FileSystem;
use SwissKnife202507\Nette\Utils\Strings;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstFinder;
use Rector\SwissKnife\Twig\TwigTemplateConstantExtractor;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\VisibilityChangeStats;
use Rector\SwissKnife\YAML\YamlConfigConstantExtractor;
use SwissKnife202507\Symfony\Component\Console\Command\Command;
use SwissKnife202507\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202507\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202507\Symfony\Component\Console\Input\InputOption;
use SwissKnife202507\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202507\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202507\Symfony\Component\Finder\SplFileInfo;
final class PrivatizeConstantsCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder
     */
    private $classConstantFetchFinder;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\Finder\ClassConstFinder
     */
    private $classConstFinder;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Twig\TwigTemplateConstantExtractor
     */
    private $twigTemplateConstantExtractor;
    /**
     * @readonly
     * @var \Rector\SwissKnife\YAML\YamlConfigConstantExtractor
     */
    private $yamlConfigConstantExtractor;
    public function __construct(SymfonyStyle $symfonyStyle, ClassConstantFetchFinder $classConstantFetchFinder, ClassConstFinder $classConstFinder, TwigTemplateConstantExtractor $twigTemplateConstantExtractor, YamlConfigConstantExtractor $yamlConfigConstantExtractor)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->classConstantFetchFinder = $classConstantFetchFinder;
        $this->classConstFinder = $classConstFinder;
        $this->twigTemplateConstantExtractor = $twigTemplateConstantExtractor;
        $this->yamlConfigConstantExtractor = $yamlConfigConstantExtractor;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('privatize-constants');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check, include tests directory as well');
        $this->addOption('exclude-path', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Path to exclude');
        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Debug output');
        $this->setDescription('Make class constants private if not used outside in PHP, Twig and YAML files');
    }
    /**
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $excludedPaths = (array) $input->getOption('exclude-path');
        $isDebug = (bool) $input->getOption('debug');
        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->symfonyStyle->warning('No PHP files found in provided paths');
            return self::SUCCESS;
        }
        $this->symfonyStyle->title('Finding class const fetches...');
        $progressBar = $this->symfonyStyle->createProgressBar(\count($phpFileInfos));
        $phpClassConstantFetches = $this->classConstantFetchFinder->find($phpFileInfos, $progressBar, $isDebug);
        // find usage in twig files
        $twigClassConstantFetches = $this->twigTemplateConstantExtractor->extractFromDirs($sources);
        $yamlClassConstantFetches = $this->yamlConfigConstantExtractor->extractFromDirs($sources);
        $classConstantFetches = \array_merge($phpClassConstantFetches, $twigClassConstantFetches, $yamlClassConstantFetches);
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success(\sprintf('Found %d class constant fetches', \count($classConstantFetches)));
        $this->symfonyStyle->success(\sprintf('Found %d constants in Twig templates', \count($twigClassConstantFetches)));
        $this->symfonyStyle->success(\sprintf('Found %d constants in YAML configs', \count($yamlClassConstantFetches)));
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('Changing class constant visibility based on use...');
        $visibilityChangeStats = new VisibilityChangeStats();
        // go file by file and deal with public + protected constants
        foreach ($phpFileInfos as $phpFileInfo) {
            $currentVisibilityChangeStats = $this->processFileInfo($phpFileInfo, $classConstantFetches);
            $visibilityChangeStats->merge($currentVisibilityChangeStats);
        }
        if (!$visibilityChangeStats->hasAnyChange()) {
            $this->symfonyStyle->warning('No constants were privatized');
            return self::SUCCESS;
        }
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success(\sprintf('Totally %d constants were made private', $visibilityChangeStats->getPrivateCount()));
        return self::SUCCESS;
    }
    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function processFileInfo(SplFileInfo $phpFileInfo, array $classConstantFetches) : VisibilityChangeStats
    {
        $visibilityChangeStats = new VisibilityChangeStats();
        $classConstants = $this->classConstFinder->find($phpFileInfo->getRealPath());
        if ($classConstants === []) {
            return $visibilityChangeStats;
        }
        foreach ($classConstants as $classConstant) {
            if ($this->isClassConstantUsedPublicly($classConstantFetches, $classConstant)) {
                // keep it public
                continue;
            }
            // make private
            $changedFileContents = Strings::replace($phpFileInfo->getContents(), '#((private|public|protected)\\s+)?const\\s+' . $classConstant->getConstantName() . '#', 'private const ' . $classConstant->getConstantName());
            FileSystem::write($phpFileInfo->getRealPath(), $changedFileContents);
            $this->symfonyStyle->writeln(\sprintf('Constant "%s" changed to private', $classConstant->getConstantName()));
            $visibilityChangeStats->countPrivate();
        }
        return $visibilityChangeStats;
    }
    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function isClassConstantUsedPublicly(array $classConstantFetches, ClassConstant $classConstant) : bool
    {
        foreach ($classConstantFetches as $classConstantFetch) {
            if (!$classConstantFetch->isClassConstantMatch($classConstant)) {
                continue;
            }
            // used only locally, can stay private
            if ($classConstantFetch instanceof CurrentClassConstantFetch) {
                continue;
            }
            // used externally, make public
            return \true;
        }
        return \false;
    }
}
