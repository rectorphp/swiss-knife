<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\Helpers\ClassNameResolver;
use Rector\SwissKnife\PHPStan\ClassConstantResultAnalyser;
use Rector\SwissKnife\Resolver\StaticClassConstResolver;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class PrivatizeConstantsCommand extends Command
{
    /**
     * @var string
     * @see https://regex101.com/r/wkHZwX/1
     */
    private const PUBLIC_CONST_REGEX = '#(    |\t)(public )?const #ms';

    /**
     * @var int
     */
    private const TIMEOUT_IN_SECONDS = 300;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ClassConstantResultAnalyser $classConstantResultAnalyser,
        private readonly StaticClassConstResolver $staticClassConstResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('privatize-constants');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check, include tests directory as well'
        );

        $this->addOption(
            'exclude-path',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Path to exclude'
        );

        $this->setDescription('Make class constants private if not used outside');
    }

    /**
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $excludedPaths = (array) $input->getOption('exclude-path');

        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->symfonyStyle->warning('No PHP files found in provided paths');

            return self::SUCCESS;
        }

        $this->symfonyStyle->success('1. Finding all class constants...');

        dump('testing');
        die;

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
            $this->symfonyStyle->success(
                sprintf('%d constant made public', $publicAndProtectedClassConstants->getPublicCount())
            );
        }

        if ($publicAndProtectedClassConstants->getProtectedCount() !== 0) {
            $this->symfonyStyle->success(
                sprintf('%d constant made protected', $publicAndProtectedClassConstants->getProtectedCount())
            );
        }

        if ($staticClassConstMatches !== []) {
            $this->symfonyStyle->success(
                \sprintf('%d constants made protected for static access', count($staticClassConstMatches))
            );
        }

        return self::SUCCESS;
    }

    private function replacePrivateConstWith(ClassConstMatch $publicClassConstMatch, string $replaceString): void
    {
        $classFileContents = FileSystem::read($publicClassConstMatch->getClassFileName());

        // replace "private const NAME" with "private const NAME"
        $classFileContents = str_replace(
            'private const ' . $publicClassConstMatch->getConstantName(),
            $replaceString . ' ' . $publicClassConstMatch->getConstantName(),
            $classFileContents
        );

        FileSystem::write($publicClassConstMatch->getClassFileName(), $classFileContents);

        // @todo handle case when "AppBundle\Rpc\BEItem\BeItemPackage::ITEM_TYPE_NAME_PACKAGE" constant is in parent class
        $parentClassConstMatch = $publicClassConstMatch->getParentClassConstMatch();
        if (! $parentClassConstMatch instanceof ClassConstMatch) {
            return;
        }

        $this->replacePrivateConstWith($parentClassConstMatch, $replaceString);
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @param ClassConstMatch[] $staticClassConstsMatches
     */
    private function replaceClassAndChildWithProtected(array $phpFileInfos, array $staticClassConstsMatches): void
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
                if (! is_a($fullyQualifiedClassName, $staticClassConstMatch->getClassName(), true)) {
                    continue;
                }

                $classFileContents = \str_replace(
                    'private const ' . $staticClassConstMatch->getConstantName(),
                    'protected const ' . $staticClassConstMatch->getConstantName(),
                    $phpFileInfo->getContents()
                );

                $this->symfonyStyle->warning(sprintf(
                    'The "%s" constant in "%s" made protected to allow static access. Consider refactoring to better design',
                    $staticClassConstMatch->getConstantName(),
                    $staticClassConstMatch->getClassName(),
                ));

                FileSystem::write($phpFileInfo->getRealPath(), $classFileContents);
            }
        }
    }
}
