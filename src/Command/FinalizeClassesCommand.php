<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Composer\Semver\Intervals;
use Composer\Semver\VersionParser;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Entropy\Utils\FileSystem;
use Entropy\Utils\Regex;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\Jack\Utils\JsonFileLoader;
use Rector\SwissKnife\MockedClassResolver;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use UnexpectedValueException;

final class FinalizeClassesCommand implements CommandInterface
{
    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    /**
     * @readonly
     */
    private ParentClassResolver $parentClassResolver;

    /**
     * @readonly
     */
    private EntityClassResolver $entityClassResolver;

    /**
     * @readonly
     */
    private CachedPhpParser $cachedPhpParser;

    /**
     * @readonly
     */
    private MockedClassResolver $mockedClassResolver;

    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     * @var string
     */
    private const NEWLINE_CLASS_START_REGEX = '#^(readonly )?class\s#m';

    private const MINIMAL_PHP_CONSTRAINT = '>=8.4.0';

    private const MINIMAL_DOCTRINE_ORM_CONSTRAINT = '>=3.4.0';

    public function __construct(OutputPrinter $outputPrinter, ParentClassResolver $parentClassResolver, EntityClassResolver $entityClassResolver, CachedPhpParser $cachedPhpParser, MockedClassResolver $mockedClassResolver)
    {
        $this->outputPrinter = $outputPrinter;
        $this->parentClassResolver = $parentClassResolver;
        $this->entityClassResolver = $entityClassResolver;
        $this->cachedPhpParser = $cachedPhpParser;
        $this->mockedClassResolver = $mockedClassResolver;
    }

    /**
     * @param string[] $paths Directories to finalize
     * @param bool $dryRun Do no change anything, only list classes about to be finalized. If there are classes to finalize, it will exit with code 1. Useful for CI.
     * @param bool $skipMocked Skip mocked classes as well (use only if unable to run bypass-finals package)
     * @param string[] $skipFiles Skip file or files by path
     * @param bool $noProgress Do not show progress bar, only results
     * @param bool $finalizeDoctrineEntities Finalize Doctrine entities too. Requires PHP 8.4+ and doctrine/orm 3.4+ in composer.json
     */
    public function run(
        array $paths,
        bool $dryRun = false,
        bool $skipMocked = false,
        array $skipFiles = [],
        bool $noProgress = false,
        bool $finalizeDoctrineEntities = false
    ): int {
        if ($finalizeDoctrineEntities && ! $this->isDoctrineEntitiesFinalizeSupported()) {
            $this->outputPrinter->error(
                '--finalize-doctrine-entities requires "php" 8.4+ and "doctrine/orm" 3.4+ in composer.json'
            );

            return ExitCode::ERROR;
        }

        $this->outputPrinter->title('1. Detecting parent and entity classes');

        $phpFileInfos = PhpFilesFinder::find($paths, $skipFiles);

        $progressBar = null;
        if (! $noProgress) {
            // double to count for both parent and entity resolver
            $stepRatio = $skipMocked ? 3 : 2;

            $progressBar = new ProgressBar();
            $progressBar->start($stepRatio * count($phpFileInfos));
        }

        $progressClosure = function () use ($noProgress, $progressBar): void {
            if ($noProgress || ! $progressBar instanceof ProgressBar) {
                return;
            }

            $progressBar->advance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($paths, $progressClosure);

        $mockedClassNames = $skipMocked ? $this->mockedClassResolver->resolve($paths, $progressClosure) : [];

        if ($progressBar instanceof ProgressBar) {
            $progressBar->finish();
        }

        $parentCount = count($parentClassNames);
        $entityCount = count($entityClassNames);
        $this->outputPrinter->writeln(sprintf(
            'Found %d parent class%s and %d entity class%s',
            $parentCount,
            $parentCount === 1 ? '' : 'es',
            $entityCount,
            $entityCount === 1 ? '' : 'es'
        ));

        if ($skipMocked) {
            $mockedCount = count($mockedClassNames);
            $this->outputPrinter->writeln(sprintf(
                'Also %d mocked class%s',
                $mockedCount,
                $mockedCount === 1 ? '' : 'es'
            ));
        }

        $this->outputPrinter->newline(1);

        $this->outputPrinter->title('2. Finalizing safe classes');

        $excludedClasses = array_merge($parentClassNames, $mockedClassNames);
        if (! $finalizeDoctrineEntities) {
            $excludedClasses = array_merge($excludedClasses, $entityClassNames);
        }

        $needsFinalizeAnalyzer = new NeedsFinalizeAnalyzer($excludedClasses, $this->cachedPhpParser);

        $finalizedFilePaths = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            // should be file be finalize, is not and is not excluded?
            if (! $needsFinalizeAnalyzer->isNeeded($phpFileInfo->getRealPath())) {
                continue;
            }

            $finalizedContents = Regex::replace(
                $phpFileInfo->getContents(),
                self::NEWLINE_CLASS_START_REGEX,
                'final $1class '
            );

            $finalizedFilePaths[] = PathHelper::relativeToCwd($phpFileInfo->getRealPath());

            if ($dryRun === false) {
                FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents);
            }
        }

        if ($finalizedFilePaths === []) {
            $this->outputPrinter->success('Nothing to finalize');
            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->listing($finalizedFilePaths);

        $countFinalizedClasses = count($finalizedFilePaths);
        $pluralClassText = $countFinalizedClasses === 1 ? 'class' : 'classes';

        // to make it fail in CI
        if ($dryRun) {
            $this->outputPrinter->error(sprintf(
                '%d %s can be finalized',
                $countFinalizedClasses,
                $pluralClassText,
            ));

            return ExitCode::ERROR;
        }

        $this->outputPrinter->success(sprintf('%d %s finalized', $countFinalizedClasses, $pluralClassText));

        return ExitCode::SUCCESS;
    }

    private function isDoctrineEntitiesFinalizeSupported(): bool
    {
        $composerJsonFilePath = getcwd() . '/composer.json';
        if (! file_exists($composerJsonFilePath)) {
            return false;
        }

        $composerJson = JsonFileLoader::loadFileToJson($composerJsonFilePath);
        $requires = (array) ($composerJson['require'] ?? []);
        $requiresDev = (array) ($composerJson['require-dev'] ?? []);

        $phpConstraint = $requires['php'] ?? null;
        $doctrineOrmConstraint = $requires['doctrine/orm'] ?? $requiresDev['doctrine/orm'] ?? null;

        return $this->isConstraintWithin($phpConstraint, self::MINIMAL_PHP_CONSTRAINT)
            && $this->isConstraintWithin($doctrineOrmConstraint, self::MINIMAL_DOCTRINE_ORM_CONSTRAINT);
    }

    /**
     * @param mixed $constraint
     */
    private function isConstraintWithin($constraint, string $minimalConstraint): bool
    {
        if (! is_string($constraint) || $constraint === '') {
            return false;
        }

        $versionParser = new VersionParser();

        try {
            return Intervals::isSubsetOf(
                $versionParser->parseConstraints($constraint),
                $versionParser->parseConstraints($minimalConstraint)
            );
        } catch (UnexpectedValueException $exception) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'finalize-classes';
    }

    public function getDescription(): string
    {
        return 'Finalize classes without children';
    }
}
