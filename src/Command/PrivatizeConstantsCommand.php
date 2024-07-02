<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202407\Nette\Utils\FileSystem;
use SwissKnife202407\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\Helpers\ClassNameResolver;
use Rector\SwissKnife\PHPStan\ClassConstantResultAnalyser;
use Rector\SwissKnife\Resolver\StaticClassConstResolver;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use SwissKnife202407\Symfony\Component\Console\Command\Command;
use SwissKnife202407\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202407\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202407\Symfony\Component\Console\Input\InputOption;
use SwissKnife202407\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202407\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202407\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202407\Symfony\Component\Process\Process;
final class PrivatizeConstantsCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PHPStan\ClassConstantResultAnalyser
     */
    private $classConstantResultAnalyser;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Resolver\StaticClassConstResolver
     */
    private $staticClassConstResolver;
    /**
     * @var string
     * @see https://regex101.com/r/wkHZwX/1
     */
    private const PUBLIC_CONST_REGEX = '#(    |\\t)(public )?const #ms';
    public function __construct(SymfonyStyle $symfonyStyle, ClassConstantResultAnalyser $classConstantResultAnalyser, StaticClassConstResolver $staticClassConstResolver)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->classConstantResultAnalyser = $classConstantResultAnalyser;
        $this->staticClassConstResolver = $staticClassConstResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('privatize-constants');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check, include tests directory as well');
        $this->addOption('exclude-path', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Path to exclude');
        $this->setDescription('Make class constants private if not used outside');
    }
    /**
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $excludedPaths = (array) $input->getOption('exclude-path');
        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->symfonyStyle->warning('No PHP files found in provided paths');
            return self::SUCCESS;
        }
        $this->privatizeClassConstants($phpFileInfos);
        // special case of self::NAME, that should be protected - their children too
        $staticClassConstMatches = $this->staticClassConstResolver->resolve($phpFileInfos);
        $phpstanResult = $this->runPHPStanAnalyse($sources);
        $publicAndProtectedClassConstants = $this->classConstantResultAnalyser->analyseResult($phpstanResult);
        if ($publicAndProtectedClassConstants->isEmpty()) {
            $this->symfonyStyle->success('No class constant visibility to change');
            return self::SUCCESS;
        }
        // make public first, to avoid override to protected
        foreach ($publicAndProtectedClassConstants->getPublicClassConstMatches() as $publicClassConstMatch) {
            $this->replacePrivateConstWith($publicClassConstMatch, 'public const');
        }
        foreach ($publicAndProtectedClassConstants->getProtectedClassConstMatches() as $publicClassConstMatch) {
            $this->replacePrivateConstWith($publicClassConstMatch, 'protected const');
        }
        $this->replaceClassAndChildWithProtected($phpFileInfos, $staticClassConstMatches);
        if ($publicAndProtectedClassConstants->getPublicCount() !== 0) {
            $this->symfonyStyle->success(\sprintf('%d constant made public', $publicAndProtectedClassConstants->getPublicCount()));
        }
        if ($publicAndProtectedClassConstants->getProtectedCount() !== 0) {
            $this->symfonyStyle->success(\sprintf('%d constant made protected', $publicAndProtectedClassConstants->getProtectedCount()));
        }
        if ($staticClassConstMatches !== []) {
            $this->symfonyStyle->success(\sprintf('%d constants made protected for static access', \count($staticClassConstMatches)));
        }
        return self::SUCCESS;
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function privatizeClassConstants(array $phpFileInfos) : void
    {
        $this->symfonyStyle->note(\sprintf('Found %d PHP files, turning constants to private', \count($phpFileInfos)));
        $privatizedFileCount = 0;
        foreach ($phpFileInfos as $phpFileInfo) {
            $originalFileContent = $phpFileInfo->getContents();
            $fileContent = $this->makeClassConstantsPrivate($originalFileContent);
            if ($originalFileContent === $fileContent) {
                continue;
            }
            FileSystem::write($phpFileInfo->getRealPath(), $fileContent);
            ++$privatizedFileCount;
        }
        $this->symfonyStyle->success(\sprintf('Constants in %d files turned to private', $privatizedFileCount));
    }
    private function makeClassConstantsPrivate(string $fileContents) : string
    {
        return Strings::replace($fileContents, self::PUBLIC_CONST_REGEX, '$1private const ');
    }
    /**
     * @param string[] $paths
     * @return array<string, mixed>
     */
    private function runPHPStanAnalyse(array $paths) : array
    {
        $this->symfonyStyle->note('Running PHPStan to spot false-private class constants');
        $phpStanAnalyseProcess = new Process(\array_merge(['vendor/bin/phpstan', 'analyse'], $paths, ['--configuration', __DIR__ . '/../../config/privatize-constants-phpstan-ruleset.neon', '--error-format', 'json']));
        $phpStanAnalyseProcess->run();
        $this->symfonyStyle->success('PHPStan analysis finished');
        // process output message
        \sleep(1);
        $this->symfonyStyle->newLine();
        $resultOutput = $phpStanAnalyseProcess->getOutput() ?: $phpStanAnalyseProcess->getErrorOutput();
        return \json_decode($resultOutput, \true);
    }
    private function replacePrivateConstWith(ClassConstMatch $publicClassConstMatch, string $replaceString) : void
    {
        $classFileContents = FileSystem::read($publicClassConstMatch->getClassFileName());
        // replace "private const NAME" with "private const NAME"
        $classFileContents = \str_replace('private const ' . $publicClassConstMatch->getConstantName(), $replaceString . ' ' . $publicClassConstMatch->getConstantName(), $classFileContents);
        FileSystem::write($publicClassConstMatch->getClassFileName(), $classFileContents);
        // @todo handle case when "AppBundle\Rpc\BEItem\BeItemPackage::ITEM_TYPE_NAME_PACKAGE" constant is in parent class
        $parentClassConstMatch = $publicClassConstMatch->getParentClassConstMatch();
        if (!$parentClassConstMatch instanceof ClassConstMatch) {
            return;
        }
        $this->replacePrivateConstWith($parentClassConstMatch, $replaceString);
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     * @param ClassConstMatch[] $staticClassConstsMatches
     */
    private function replaceClassAndChildWithProtected(array $phpFileInfos, array $staticClassConstsMatches) : void
    {
        if ($staticClassConstsMatches === []) {
            return;
        }
        foreach ($phpFileInfos as $phpFileInfo) {
            $fullyQualifiedClassName = ClassNameResolver::resolveFromFileContents($phpFileInfo->getContents());
            if ($fullyQualifiedClassName === null) {
                // no class to process
                continue;
            }
            foreach ($staticClassConstsMatches as $staticClassConstMatch) {
                // update current and all hcildren
                if (!\is_a($fullyQualifiedClassName, $staticClassConstMatch->getClassName(), \true)) {
                    continue;
                }
                $classFileContents = \str_replace('private const ' . $staticClassConstMatch->getConstantName(), 'protected const ' . $staticClassConstMatch->getConstantName(), $phpFileInfo->getContents());
                $this->symfonyStyle->warning(\sprintf('The "%s" constant in "%s" made protected to allow static access. Consider refactoring to better design', $staticClassConstMatch->getConstantName(), $staticClassConstMatch->getClassName()));
                FileSystem::write($phpFileInfo->getRealPath(), $classFileContents);
            }
        }
    }
}
