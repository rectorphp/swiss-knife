<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Behastan\Command;

use Rector\SwissKnife\Behastan\Behastan;
use Rector\SwissKnife\Behastan\Finder\BehatMetafilesFinder;
use Rector\SwissKnife\Behastan\ValueObject\Mask\AbstractMask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class BehastanCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly BehatMetafilesFinder $behatMetafilesFinder,
        private readonly Behastan $behastan,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('behastan');

        $this->setDescription('Checks Behat definitions in *Context.php files and feature files to spot unused ones');

        $this->addArgument(
            'test-directory',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check or *.Context.php and feature.yml files'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $testDirectories = (array) $input->getArgument('test-directory');
        Assert::allDirectory($testDirectories);

        $featureFiles = $this->behatMetafilesFinder->findFeatureFiles($testDirectories);
        if ($featureFiles === []) {
            $this->symfonyStyle->error('No *.feature files found. Please provide correct test directory');
            return self::FAILURE;
        }

        $contextFiles = $this->behatMetafilesFinder->findContextFiles($testDirectories);
        if ($contextFiles === []) {
            $this->symfonyStyle->error('No *Context.php files found. Please provide correct test directory');
            return self::FAILURE;
        }

        $this->symfonyStyle->title(
            sprintf('Checking static, named and regex masks from %d *Feature files', count($featureFiles))
        );

        $unusedMasks = $this->behastan->analyse($contextFiles, $featureFiles);

        $this->symfonyStyle->newLine(2);

        if ($unusedMasks === []) {
            $this->symfonyStyle->success('All definitions are used');
            return Command::SUCCESS;
        }

        $this->reportUnusedDefinitions($unusedMasks);

        return Command::FAILURE;
    }

    /**
     * @param AbstractMask[] $unusedMasks
     */
    private function reportUnusedDefinitions(array $unusedMasks): void
    {
        foreach ($unusedMasks as $unusedMask) {
            $this->printMask($unusedMask);
        }

        $this->symfonyStyle->error(sprintf('Found %d unused definitions', count($unusedMasks)));
    }

    private function printMask(AbstractMask $unusedMask): void
    {
        $this->symfonyStyle->writeln($unusedMask->mask);

        // make path relative
        $relativeFilePath = str_replace(getcwd() . '/', '', $unusedMask->filePath);
        $this->symfonyStyle->writeln($relativeFilePath);
        $this->symfonyStyle->newLine();
    }
}
