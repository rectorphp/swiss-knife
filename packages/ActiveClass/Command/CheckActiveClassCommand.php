<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\Command;

use EasyCI202301\Symfony\Component\Console\Command\Command;
use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202301\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter;
use Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder;
use Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI202301\Symplify\SmartFileSystem\Finder\SmartFinder;
final class CheckActiveClassCommand extends Command
{
    /**
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    private $smartFinder;
    /**
     * @var \Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder
     */
    private $classNamesFinder;
    /**
     * @var \Symplify\EasyCI\ActiveClass\UseImportsResolver
     */
    private $useImportsResolver;
    /**
     * @var \Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter
     */
    private $possiblyUnusedClassesFilter;
    /**
     * @var \Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter
     */
    private $unusedClassReporter;
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SmartFinder $smartFinder, ClassNamesFinder $classNamesFinder, UseImportsResolver $useImportsResolver, PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, UnusedClassReporter $unusedClassReporter, ParameterProvider $parameterProvider, SymfonyStyle $symfonyStyle)
    {
        $this->smartFinder = $smartFinder;
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->parameterProvider = $parameterProvider;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-active-class');
        $this->setDescription('Check classes that are not used in any config and in the code');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths with templates');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(Option::EXCLUDED_CHECK_PATHS);
        $sources = (array) $input->getArgument(Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', $excludedCheckPaths);
        $phpFilesCount = \count($phpFileInfos);
        $this->symfonyStyle->progressStart($phpFilesCount);
        $usedNames = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFileInfo);
            $usedNames = \array_merge($usedNames, $currentUsedNames);
            $this->symfonyStyle->progressAdvance();
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFileInfos);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames);
        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }
}
