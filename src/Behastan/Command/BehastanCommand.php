<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Behastan\Command;

use Nette\Utils\Strings;
use Rector\SwissKnife\Behastan\DefinitionMasksResolver;
use Rector\SwissKnife\Behastan\Finder\BehatMetafilesFinder;
use Rector\SwissKnife\Behastan\UsedInstructionResolver;
use Rector\SwissKnife\Behastan\ValueObject\AbstractMask;
use Rector\SwissKnife\Behastan\ValueObject\ExactMask;
use Rector\SwissKnife\Behastan\ValueObject\MaskCollection;
use Rector\SwissKnife\Behastan\ValueObject\NamedMask;
use Rector\SwissKnife\Behastan\ValueObject\RegexMask;
use Rector\SwissKnife\Behastan\ValueObject\SkippedMask;
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
        private readonly DefinitionMasksResolver $definitionMasksResolver,
        private readonly UsedInstructionResolver $usedInstructionResolver,
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
        if (count($featureFiles) === 0) {
            $this->symfonyStyle->error('No *.feature files found. Please provide correct test directory');
            return self::FAILURE;
        }

        $contextFiles = $this->behatMetafilesFinder->findContextFiles($testDirectories);
        if (count($contextFiles) === 0) {
            $this->symfonyStyle->error('No *Context.php files found. Please provide correct test directory');
            return self::FAILURE;
        }

        $this->symfonyStyle->title(
            sprintf('Checking static, named and regex masks from %d *Feature files', count($featureFiles))
        );

        $maskCollection = $this->definitionMasksResolver->resolve($contextFiles);
        $this->printStats($maskCollection);

        $featureInstructions = $this->usedInstructionResolver->resolveInstructionsFromFeatureFiles($featureFiles);

        $maskProgressBar = $this->symfonyStyle->createProgressBar($maskCollection->count());

        $unusedMasks = [];
        foreach ($maskCollection->all() as $mask) {
            $maskProgressBar->advance();

            if ($mask instanceof SkippedMask) {
                continue;
            }

            // is used?
            if ($mask instanceof ExactMask && in_array($mask->mask, $featureInstructions, true)) {
                continue;
            }

            // is used?
            if ($mask instanceof RegexMask && $this->isRegexDefinitionUsed($mask->mask, $featureInstructions)) {
                continue;
            }

            if ($mask instanceof NamedMask) {
                // normalize :mask definition to regex
                $regexMask = '#' . Strings::replace($mask->mask, '#(\:[\W\w]+)#', '(.*?)') . '#';
                if ($this->isRegexDefinitionUsed($regexMask, $featureInstructions)) {
                    continue;
                }
            }

            if ($mask instanceof AbstractMask) {
                $unusedMasks[] = $mask;
            }
        }

        $maskProgressBar->finish();
        $this->symfonyStyle->newLine(2);

        if ($unusedMasks === []) {
            $this->symfonyStyle->success('All definitions are used');
            return Command::SUCCESS;
        }

        $this->reportUnusedDefinitions($unusedMasks);

        return Command::FAILURE;
    }

    /**
     * @param string[] $featureInstructions
     */
    private function isRegexDefinitionUsed(string $regexBehatDefinition, array $featureInstructions): bool
    {
        foreach ($featureInstructions as $featureInstruction) {
            if (Strings::match($featureInstruction, $regexBehatDefinition)) {
                // it is used!
                return true;
            }
        }

        return false;
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

    private function printStats(MaskCollection $maskCollection): void
    {
        $this->symfonyStyle->writeln(sprintf('Found %d masks:', $maskCollection->count()));
        $this->symfonyStyle->newLine();

        $this->symfonyStyle->writeln(sprintf(' * %d exact', $maskCollection->countByType(ExactMask::class)));
        $this->symfonyStyle->writeln(sprintf(' * %d /regex/', $maskCollection->countByType(RegexMask::class)));
        $this->symfonyStyle->writeln(sprintf(' * %d :named', $maskCollection->countByType(NamedMask::class)));
        $this->symfonyStyle->writeln(sprintf(' * %d skipped', $maskCollection->countByType(SkippedMask::class)));

        $skippedMasks = $maskCollection->byType(SkippedMask::class);
        if ($skippedMasks !== []) {
            $this->symfonyStyle->newLine();

            foreach ($skippedMasks as $skippedMask) {
                $this->printMask($skippedMask);
            }

            $this->symfonyStyle->newLine();
        }
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
