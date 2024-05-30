<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202405\Nette\Utils\FileSystem;
use SwissKnife202405\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\PHPStan\ClassConstantResultAnalyser;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use SwissKnife202405\Symfony\Component\Console\Command\Command;
use SwissKnife202405\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202405\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202405\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202405\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202405\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202405\Symfony\Component\Process\Process;
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
     * @var string
     * @see https://regex101.com/r/wkHZwX/1
     */
    private const PUBLIC_CONST_REGEX = '#(    |\\t)(public )?const #ms';
    public function __construct(SymfonyStyle $symfonyStyle, ClassConstantResultAnalyser $classConstantResultAnalyser)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->classConstantResultAnalyser = $classConstantResultAnalyser;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('privatize-constants');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check, include tests directory as well');
        $this->setDescription('Make class constants private if not used outside');
    }
    /**
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $phpFileInfos = FilesFinder::findPhpFiles($sources);
        $this->privatizeClassConstants($phpFileInfos);
        $phpstanResult = $this->runPHPStanAnalyse($sources);
        $publicAndProtectedClassConstants = $this->classConstantResultAnalyser->analyseResult($phpstanResult);
        if ($publicAndProtectedClassConstants->isEmpty()) {
            $this->symfonyStyle->success('No class constant visibility to change');
            return self::SUCCESS;
        }
        // make public first, to avoid override to protected
        foreach ($publicAndProtectedClassConstants->getPublicClassConstMatch() as $publicClassConstMatch) {
            $this->replacePrivateConstWith($publicClassConstMatch, 'public const');
        }
        foreach ($publicAndProtectedClassConstants->getProtectedClassConstMatch() as $publicClassConstMatch) {
            $this->replacePrivateConstWith($publicClassConstMatch, 'protected const');
        }
        $this->symfonyStyle->success(\sprintf('%d constant made public', $publicAndProtectedClassConstants->getPublicCount()));
        $this->symfonyStyle->success(\sprintf('%d constant made protected', $publicAndProtectedClassConstants->getProtectedCount()));
        return self::SUCCESS;
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function privatizeClassConstants(array $phpFileInfos) : void
    {
        foreach ($phpFileInfos as $phpFileInfo) {
            $originalFileContent = $phpFileInfo->getContents();
            $fileContent = $this->makeClassConstantsPrivate($originalFileContent);
            if ($originalFileContent === $fileContent) {
                continue;
            }
            FileSystem::write($phpFileInfo->getRealPath(), $fileContent);
            $this->symfonyStyle->note(\sprintf('Constants in "%s" file privatized', $phpFileInfo->getRelativePathname()));
        }
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
        $resultOutput = $phpStanAnalyseProcess->getOutput() ?: $phpStanAnalyseProcess->getErrorOutput();
        return \json_decode($resultOutput, \true);
    }
    private function replacePrivateConstWith(ClassConstMatch $publicClassConstMatch, string $replaceString) : void
    {
        $classFileContents = FileSystem::read($publicClassConstMatch->getClassFileName());
        // replace "private const NAME" with "private const NAME"
        $classFileContents = \str_replace('private const ' . $publicClassConstMatch->getConstantName(), $replaceString . ' ' . $publicClassConstMatch->getConstantName(), $classFileContents);
        FileSystem::write($publicClassConstMatch->getClassFileName(), $classFileContents);
    }
}
