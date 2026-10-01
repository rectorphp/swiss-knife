<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Command;

use DateTimeImmutable;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Utils\Json;
use Rector\SwissKnife\Jack\Composer\ComposerOutdatedResponseProvider;
use Rector\SwissKnife\Jack\Enum\ComposerKey;
use Rector\SwissKnife\Jack\OutdatedComposerFactory;

final class BreakPointCommand implements CommandInterface
{
    private OutdatedComposerFactory $outdatedComposerFactory;

    private ComposerOutdatedResponseProvider $composerOutdatedResponseProvider;

    private OutputPrinter $outputPrinter;

    public function __construct(
        OutdatedComposerFactory $outdatedComposerFactory,
        ComposerOutdatedResponseProvider $composerOutdatedResponseProvider,
        OutputPrinter $outputPrinter
    ) {
        $this->outdatedComposerFactory = $outdatedComposerFactory;
        $this->composerOutdatedResponseProvider = $composerOutdatedResponseProvider;
        $this->outputPrinter = $outputPrinter;
    }

    /**
     * @param bool $dev Focus on dev packages only
     * @param int $limit Maximum number of outdated major version packages
     * @param int $minDays Minimum number of days a release has to be old to be considered outdated
     * @param string[] $ignore Ignore packages by name, e.g. "symfony/" or "symfony/console"
     */
    public function run(bool $dev = false, int $limit = 5, int $minDays = 0, array $ignore = []): int
    {
        $this->outputPrinter->green('Analyzing "composer.json" for major and minor outdated packages');

        $responseJsonContents = $this->composerOutdatedResponseProvider->provide();

        $responseJson = Json::decode($responseJsonContents);
        if (! isset($responseJson[ComposerKey::INSTALLED_KEY])) {
            $this->outputPrinter->green('All packages are up to date');

            return ExitCode::SUCCESS;
        }

        $composerJsonFilePath = getcwd() . '/composer.json';
        $now = new DateTimeImmutable();
        $outdatedComposer = $this->outdatedComposerFactory->createOutdatedComposer(
            array_filter(
                $responseJson[ComposerKey::INSTALLED_KEY],
                static function (array $package) use ($ignore, $minDays, $now): bool {
                    foreach ($ignore as $ignoredPackage) {
                        if (strpos((string) $package['name'], $ignoredPackage) !== false) {
                            return false;
                        }
                    }

                    if ($minDays === 0) {
                        return true;
                    }

                    $latestReleaseDate = new DateTimeImmutable($package['latest-release-date']);
                    $pageAgeInDays = $latestReleaseDate->diff($now)
                        ->days;
                    return $pageAgeInDays >= $minDays;
                }
            ),
            $composerJsonFilePath
        );

        if ($outdatedComposer->count() === 0) {
            $this->outputPrinter->greenBackground('All packages are up to date');
            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->yellow(
            sprintf(
                'Found %d outdated package%s',
                $outdatedComposer->count($dev),
                $outdatedComposer->count($dev) > 1 ? 's' : ''
            )
        );
        $this->outputPrinter->newline();

        foreach ($outdatedComposer->getPackages($dev) as $outdatedPackage) {
            $this->outputPrinter->writeln(
                sprintf('The "<fg=green>%s</>" package is outdated', $outdatedPackage->getName())
            );

            if ($outdatedPackage->getCurrentVersionAge()) {
                $this->outputPrinter->writeln(sprintf(
                    ' * Your version %s is <fg=%s>%s</>',
                    $outdatedPackage->getCurrentVersion(),
                    $outdatedPackage->isVeryOld() ? 'red' : 'yellow',
                    $outdatedPackage->getCurrentVersionAge(),
                ));
            } else {
                // composer 2.7- compatible
                $this->outputPrinter->writeln(sprintf(' * Your version is %s', $outdatedPackage->getCurrentVersion()));
            }

            $this->outputPrinter->writeln(sprintf(' * Bump to %s', $outdatedPackage->getLatestVersion()));
            $this->outputPrinter->newLine();
        }

        $this->outputPrinter->newLine();
        if ($outdatedComposer->count() >= $limit) {
            // to much → fail
            $this->outputPrinter->redBackground(sprintf(
                'There %s %d outdated package%s. Update couple of them to get under %d limit',
                $outdatedComposer->count() > 1 ? 'are' : 'is',
                $outdatedComposer->count(),
                $outdatedComposer->count() > 1 ? 's' : '',
                $limit
            ));

            return ExitCode::ERROR;
        }

        if ($outdatedComposer->count() > max(1, $limit - 5)) {
            // to much → fail
            $this->outputPrinter->orangeBackground(sprintf(
                'There are %d outdated packages. Soon, the count will go over %d limit and this job will fail.%sUpgrade in time',
                $outdatedComposer->count(),
                $limit,
                PHP_EOL
            ));

            return ExitCode::SUCCESS;
        }

        // to many → fail
        $this->outputPrinter->greenBackground(
            sprintf('Still far away from limit %d. Good job keeping your project up to date!', $limit)
        );

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'breakpoint';
    }

    public function getDescription(): string
    {
        return 'Let your CI tell you, if there are too many major-version outdated packages';
    }
}
