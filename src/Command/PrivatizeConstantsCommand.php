<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Rector\SwissKnife\Finder\FilesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

final class PrivatizeConstantsCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('privatize-constants');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check'
        );
        $this->setDescription('Make class constants private if not used outside');
    }

    /**
     * @return self::*
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. find all constants with public or no type
        // 2. make them private
        // 3. run phsptan with not accessible constnat rule
        // 4. turn those reported to public again

        $sources = (array) $input->getArgument('sources');
        $phpFileInfos = FilesFinder::findPhpFiles($sources);

        foreach ($phpFileInfos as $phpFileInfo) {
            // parse and update with node visitor
            // use str_replace?
            $originalFileContent = $phpFileInfo->getContents();

            // turn all constants to private ones
            $fileContent = preg_replace('#^    const#', 'private const', $originalFileContent);
            $fileContent = str_replace('public const ', 'private const ', $fileContent);

            // has changed?
            if ($originalFileContent === $fileContent) {
                continue;
            }

            // store new version
            FileSystem::write($phpFileInfo->getRealPath(), $fileContent);
            $this->symfonyStyle->note(
                sprintf('Constants in "%s" file privatized', $phpFileInfo->getRelativePathname())
            );
        }

        // 2. run PHPStan result
        $phpStanExtensionsConfig = getcwd() . '/vendor/phpstan/extension-installer/src/GeneratedConfig.php';

        // disable phpstan extensions for this run
        $hasProjectPHPStanExtensionInstallerConfig = file_exists($phpStanExtensionsConfig);

        if ($hasProjectPHPStanExtensionInstallerConfig) {
            $changedFileContents = str_replace(
                'public const EXTENSIONS = array (',
                'public const EXTENSIONS = array (); public const EXTENSIONS_BACKUP = array (',
                FileSystem::read($phpStanExtensionsConfig)
            );
            FileSystem::write($phpStanExtensionsConfig, $changedFileContents);
        }

        $phpStanAnalyseProcess = new Process([
            'vendor/bin/phpstan',
            'analyse',
            'src',
            '--configuration',
            'config/privatize-constants-phpstan-ruleset.neon',
            '--error-format',
            'json',
        ]);
        $phpStanAnalyseProcess->run();

        // restore phpstan extensions for this run
        if ($hasProjectPHPStanExtensionInstallerConfig) {
            $changedFileContents = str_replace(
                'public const EXTENSIONS = array (); public const EXTENSIONS_BACKUP = array (',
                'public const EXTENSIONS = array (',
                file_get_contents($phpStanExtensionsConfig)
            );
            FileSystem::write($phpStanExtensionsConfig, $changedFileContents);
        }

        $phpstanResultOutput = $phpStanAnalyseProcess->getOutput() ?: $phpStanAnalyseProcess->getErrorOutput();
        $phpstanResult = json_decode($phpstanResultOutput, true);

        foreach ($phpstanResult['files'] as $detail) {
            foreach ($detail['messages'] as $messageError) {
                if (! str_contains($messageError['message'], 'Access to private constant')) {
                    continue;
                }

                // resolve message erorr details
                $match = \Nette\Utils\Strings::match(
                    $messageError['message'],
                    '#constant (?<constant_name>.*?) of class (?<class_name>[\w\\\\]+)#'
                );
                $constantName = $match['constant_name'];
                $class = $match['class_name'];

                $classReflection = new \ReflectionClass($class);
                $classFileContents = FileSystem::read($classReflection->getFileName());

                // replace "private const NAME" with "public const NAME"
                $changedFileContent = str_replace(
                    'private const ' . $constantName,
                    'public const ' . $constantName,
                    $classFileContents
                );
                if ($changedFileContent === $classFileContents) {
                    continue;
                }

                FileSystem::write($classReflection->getFileName(), $changedFileContent);

                $this->symfonyStyle->note(sprintf(
                    'Updated "%s" constant in "%s" file to public as used outside',
                    $constantName,
                    $classReflection->getFileName()
                ));
            }
        }

        return self::SUCCESS;
    }
}
