<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Utils\FileSystem;
use Entropy\Utils\Json;
use Rector\SwissKnife\Jack\Composer\ComposerOutdatedResponseProvider;
use Rector\SwissKnife\Jack\ComposerProcessor\OpenVersionsComposerProcessor;
use Rector\SwissKnife\Jack\Enum\ComposerKey;
use Rector\SwissKnife\Jack\OutdatedComposerFactory;

final class OpenVersionsCommand implements CommandInterface
{
    private OutdatedComposerFactory $outdatedComposerFactory;

    private ComposerOutdatedResponseProvider $composerOutdatedResponseProvider;

    private OpenVersionsComposerProcessor $openVersionsComposerProcessor;

    private OutputPrinter $outputPrinter;

    public function __construct(
        OutdatedComposerFactory $outdatedComposerFactory,
        ComposerOutdatedResponseProvider $composerOutdatedResponseProvider,
        OpenVersionsComposerProcessor $openVersionsComposerProcessor,
        OutputPrinter $outputPrinter
    ) {
        $this->outdatedComposerFactory = $outdatedComposerFactory;
        $this->composerOutdatedResponseProvider = $composerOutdatedResponseProvider;
        $this->openVersionsComposerProcessor = $openVersionsComposerProcessor;
        $this->outputPrinter = $outputPrinter;
    }

    /**
     * @param int $limit How many packages to open-up
     * @param bool $dryRun Without any "composer.json" changes
     * @param bool $dev Focus on dev packages only
     * @param ?string $packagePrefix Name prefix to filter packages by
     *
     * @return ExitCode::*
     */
    public function run(int $limit = 5, bool $dryRun = false, bool $dev = false, ?string $packagePrefix = null): int
    {
        $composerJsonFilePath = getcwd() . '/composer.json';

        $this->outputPrinter->green('Analyzing "composer.json" for outdated packages');

        $responseJsonContents = $this->composerOutdatedResponseProvider->provide();

        $responseJson = Json::decode($responseJsonContents);
        if (! isset($responseJson[ComposerKey::INSTALLED_KEY])) {
            $this->outputPrinter->greenBackground('All packages are up to date. You are the best!');

            return ExitCode::SUCCESS;
        }

        $outdatedComposer = $this->outdatedComposerFactory->createOutdatedComposer(
            $responseJson[ComposerKey::INSTALLED_KEY],
            $composerJsonFilePath
        );

        if ($outdatedComposer->count() === 0) {
            $this->outputPrinter->greenBackground('All packages are up to date. You are the best!');

            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->newLine();

        $this->outputPrinter->writeln(
            sprintf(
                'Found <fg=yellow>%d outdated package%s</>',
                $outdatedComposer->count(),
                $outdatedComposer->count() === 1 ? '' : 's'
            )
        );

        $this->outputPrinter->writeln(sprintf(
            ' * %d prod package%s',
            $outdatedComposer->getProdPackagesCount(),
            $outdatedComposer->getProdPackagesCount() === 1 ? '' : 's'
        ));

        $this->outputPrinter->writeln(sprintf(
            ' * %d dev package%s',
            $outdatedComposer->getDevPackagesCount(),
            $outdatedComposer->getDevPackagesCount() === 1 ? '' : 's'
        ), 1);

        $this->outputPrinter->yellow('Opening version constraints in "composer.json"');
        $this->outputPrinter->yellow('==============================================');

        $composerJsonContents = FileSystem::read($composerJsonFilePath);

        $changedPackageVersionsResult = $this->openVersionsComposerProcessor->process(
            $composerJsonContents,
            $outdatedComposer,
            $limit,
            $dev,
            $packagePrefix
        );

        $openedPackages = $changedPackageVersionsResult->getChangedPackageVersions();
        $changedComposerJson = $changedPackageVersionsResult->getComposerJsonContents();

        if ($dryRun === false) {
            // update composer.json file, only if no --dry-run
            FileSystem::write($composerJsonFilePath, rtrim($changedComposerJson) . PHP_EOL);
        }

        $this->outputPrinter->greenBackground(
            sprintf(
                '%d package%s %s opened up to the next nearest version.%s%s "composer update" to push versions up',
                count($openedPackages),
                count($openedPackages) === 1 ? '' : 's',
                $dryRun ? 'would be (is "--dry-run")' : ((count($openedPackages) === 1) ? 'was' : 'were'),
                PHP_EOL,
                $dryRun ? 'Then you would run' : 'Now run'
            )
        );

        $this->outputPrinter->newline();

        foreach ($openedPackages as $openedPackage) {
            $this->outputPrinter->writeln(sprintf(
                ' * Opened "<fg=green>%s</>" package to "<fg=yellow>%s</>" version',
                $openedPackage->getPackageName(),
                $openedPackage->getNewVersion()
            ));
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'open-versions';
    }

    public function getDescription(): string
    {
        return 'Open composer.json version constraints to the very near next version';
    }
}
