<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class PrivatizeConstantsCommand extends Command
{
    /**
     * @var string
     * @see https://regex101.com/r/VR8VUD/1
     */
    private const PRIVATE_CONSTANT_MESSAGE_REGEX = '#constant (?<constant_name>.*?) of class (?<class_name>[\w\\\\]+)#';

    /**
     * @var string
     * @see https://regex101.com/r/VR8VUD/1
     */
    private const PROTECTED_CONSTANT_MESSAGE_REGEX = '#Access to undefined constant (?<class_name>[\w\\\\]+)::(?<constant_name>.*?)#';

    /**
     * @var string
     * @see https://regex101.com/r/wkHZwX/1
     */
    private const CONST_REGEX = '#(    |\t)(public )?const #ms';

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    public function resolveProtectedClassConstMatch(string $errorMessage): ?ClassConstMatch
    {
        if (! str_contains($errorMessage, 'Access to undefined constant')) {
            return null;
        }

        $match = \Nette\Utils\Strings::match($errorMessage, self::PROTECTED_CONSTANT_MESSAGE_REGEX);
        if (! isset($match['constant_name'], $match['class_name'])) {
            return null;
        }

        /** @var class-string $className */
        $className = (string) $match['class_name'];
        return new ClassConstMatch($className, (string) $match['constant_name']);
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
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $phpFileInfos = FilesFinder::findPhpFiles($sources);

        $this->privatizeClassConstants($phpFileInfos);

        $phpstanResult = $this->runPHPStanAnalyse($sources);

        foreach ($phpstanResult['files'] as $filePath => $detail) {
            foreach ($detail['messages'] as $messageError) {
                // resolve errorMessage error details
                $publicClassConstMatch = $this->resolvePublicClassConstMatch($messageError['message']);
                $protectedClassConstMatch = $this->resolveProtectedClassConstMatch($messageError['message']);
                if (! $publicClassConstMatch instanceof ClassConstMatch && ! $protectedClassConstMatch instanceof ClassConstMatch) {
                    continue;
                }

                $classFileContents = FileSystem::read($filePath);

                if ($publicClassConstMatch instanceof ClassConstMatch) {
                    // replace "private const NAME" with "public const NAME"
                    $classFileContents = str_replace(
                        'private const ' . $publicClassConstMatch->getConstantName(),
                        'public const ' . $publicClassConstMatch->getConstantName(),
                        $classFileContents
                    );
                }

                if ($protectedClassConstMatch instanceof ClassConstMatch) {
                    // replace "private const NAME" with "protected const NAME"
                    $classFileContents = str_replace(
                        'private const ' . $protectedClassConstMatch->getConstantName(),
                        'protected const ' . $protectedClassConstMatch->getConstantName(),
                        $classFileContents
                    );
                }

                FileSystem::write($filePath, $classFileContents);
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function privatizeClassConstants(array $phpFileInfos): void
    {
        foreach ($phpFileInfos as $phpFileInfo) {
            $originalFileContent = $phpFileInfo->getContents();

            $fileContent = $this->makeClassConstantsPrivate($originalFileContent);
            if ($originalFileContent === $fileContent) {
                continue;
            }

            FileSystem::write($phpFileInfo->getRealPath(), $fileContent);

            $this->symfonyStyle->note(
                sprintf('Constants in "%s" file privatized', $phpFileInfo->getRelativePathname())
            );
        }
    }

    private function makeClassConstantsPrivate(string $fileContents): string
    {
        $fileContent = Strings::replace($fileContents, self::CONST_REGEX, '$1private const ');

        return str_replace('public const ', 'private const ', $fileContent);
    }

    /**
     * @param string[] $paths
     * @return array<string, mixed>
     */
    private function runPHPStanAnalyse(array $paths): array
    {
        $this->symfonyStyle->note('Running PHPStan to spot false-private class constants');

        $phpStanAnalyseProcess = new Process([
            'vendor/bin/phpstan',
            'analyse',
            ...$paths,
            '--configuration',
            __DIR__ . '/../../config/privatize-constants-phpstan-ruleset.neon',
            '--error-format',
            'json',
        ]);
        $phpStanAnalyseProcess->run();

        $resultOutput = $phpStanAnalyseProcess->getOutput() ?: $phpStanAnalyseProcess->getErrorOutput();
        return json_decode($resultOutput, true);
    }

    private function resolvePublicClassConstMatch(string $errorMessage): ?ClassConstMatch
    {
        if (! str_contains($errorMessage, 'Access to private constant')) {
            return null;
        }

        $match = Strings::match($errorMessage, self::PRIVATE_CONSTANT_MESSAGE_REGEX);
        if (! isset($match['constant_name'], $match['class_name'])) {
            return null;
        }

        /** @var class-string $className */
        $className = (string) $match['class_name'];

        return new ClassConstMatch($className, (string) $match['constant_name']);
    }
}
