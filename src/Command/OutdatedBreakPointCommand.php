<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use Rector\SwissKnife\ValueObject\OutdatedPackage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

final class OutdatedBreakPointCommand extends Command
{
    private const INSTALLED_KEY = 'installed';

    protected function configure(): void
    {
        $this->setName('outdated-breakpoint');

        $this->setDescription('Keep your major-version outdated packages low and check in CI');

        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of outdated major version packages',
            10
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxOutdatePackages = (int) $input->getOption('limit');

        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Analyzing "composer.json" for major outdated packages');

        $responseJsonContents = $this->loadComposerOutdatedResponse();

        $symfonyStyle->success('Done');
        $symfonyStyle->newLine();

        $responseJson = Json::decode($responseJsonContents, true);
        if (! isset($responseJson[self::INSTALLED_KEY])) {
            $symfonyStyle->success('All packages are up to date');

            return self::SUCCESS;
        }

        $outdatedPackages = $this->mapToOutdatedPackages($responseJson[self::INSTALLED_KEY]);
        $outdatedPackageCount = count($outdatedPackages);

        $symfonyStyle->title(
            sprintf('Found %d outdated package%s', $outdatedPackageCount, $outdatedPackageCount > 1 ? 's' : '')
        );

        foreach ($outdatedPackages as $outdatedPackage) {
            $symfonyStyle->writeln(sprintf('The "<fg=green>%s</>" package is outdated', $outdatedPackage->name));

            $tooOld = false;
            $matchYears = Strings::match($outdatedPackage->installedAge, '#[3-9] years#');
            if ($matchYears !== null) {
                $tooOld = true;
            }

            $symfonyStyle->writeln(sprintf(
                ' * Your version %s is <fg=%s>%s</>',
                $outdatedPackage->installedVersion,
                $tooOld ? 'red' : 'yellow',
                $outdatedPackage->installedAge,
            ));

            $symfonyStyle->writeln(sprintf(' * Bump to %s', $outdatedPackage->latestVersion));
            $symfonyStyle->newLine();
        }

        $symfonyStyle->newLine();
        if ($outdatedPackageCount >= $maxOutdatePackages) {
            // to much → fail
            $symfonyStyle->error(sprintf(
                'There %s %d outdated package%s. Update couple of them to get under %d limit',
                $outdatedPackageCount > 1 ? 'are' : 'is',
                $outdatedPackageCount,
                $outdatedPackageCount > 1 ? 's' : '',
                $maxOutdatePackages
            ));
            return self::FAILURE;
        }

        if ($outdatedPackageCount > max(1, $maxOutdatePackages - 5)) {
            // to much → fail
            $symfonyStyle->warning(sprintf(
                'There are %d outdated packages. Soon, the count will go over %d limit and this job will fail.%sUpgrade in time',
                $outdatedPackageCount,
                $maxOutdatePackages,
                PHP_EOL
            ));

            return self::SUCCESS;
        }

        // to much → fail
        $symfonyStyle->writeln(sprintf('<fg=yellow>Found %d outdated packages</>', $outdatedPackageCount));
        $symfonyStyle->newLine();

        return self::SUCCESS;
    }

    private function loadComposerOutdatedResponse(): string
    {
        $composerOutdatedProcess = Process::fromShellCommandline(
            'composer outdated --no-dev --direct --major-only --format json'
        );

        $composerOutdatedProcess->mustRun();

        return $composerOutdatedProcess->getOutput();
    }

    /**
     * @param mixed[] $packagesData
     * @return OutdatedPackage[]
     */
    private function mapToOutdatedPackages(array $packagesData): array
    {
        $outdatedPackages = [];

        foreach ($packagesData as $packageData) {
            $outdatedPackages[] = new OutdatedPackage(
                $packageData['name'],
                $packageData['latest'],
                $packageData['version'],
                $packageData['release-age'],
            );
        }

        return $outdatedPackages;
    }
}
