<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\ActiveClass\Command;

use EasyCI20220116\Symfony\Component\Console\Command\Command;
use EasyCI20220116\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220116\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220116\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220116\Symfony\Component\Console\Style\SymfonyStyle;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\UseImportsResolver;
use EasyCI20220116\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220116\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220116\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI20220116\Symplify\SmartFileSystem\Finder\SmartFinder;
final class CheckActiveClassCommand extends \EasyCI20220116\Symfony\Component\Console\Command\Command
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
    public function __construct(\EasyCI20220116\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder, \EasyCI20220116\Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder $classNamesFinder, \EasyCI20220116\Symplify\EasyCI\ActiveClass\UseImportsResolver $useImportsResolver, \EasyCI20220116\Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, \EasyCI20220116\Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter $unusedClassReporter, \EasyCI20220116\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \EasyCI20220116\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
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
        $this->setName(\EasyCI20220116\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Check classes that are not used in any config and in the code');
        $this->addArgument(\EasyCI20220116\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220116\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220116\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more paths with templates');
    }
    protected function execute(\EasyCI20220116\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220116\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(\EasyCI20220116\Symplify\EasyCI\ValueObject\Option::EXCLUDED_CHECK_PATHS);
        $sources = (array) $input->getArgument(\EasyCI20220116\Symplify\EasyCI\ValueObject\Option::SOURCES);
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
