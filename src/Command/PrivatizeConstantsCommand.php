<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202405\Nette\Utils\FileSystem;
use SwissKnife202405\Nette\Utils\Strings;
use Rector\SwissKnife\Finder\FilesFinder;
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
     * @var string
     * @see https://regex101.com/r/VR8VUD/1
     */
    private const CONSTANT_MESSAGE_REGEX = '#constant (?<constant_name>.*?) of class (?<class_name>[\\w\\\\]+)#';
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('privatize-constants');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check');
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
        foreach ($phpstanResult['files'] as $detail) {
            foreach ($detail['messages'] as $messageError) {
                // @todo check non-existing constants on child/parent access as well
                // resolve errorMessage error details
                $classConstMatch = $this->resolveClassConstMatch($messageError['errorMessage']);
                if (!$classConstMatch instanceof ClassConstMatch) {
                    continue;
                }
                $classFileContents = FileSystem::read($classConstMatch->getClassFileName());
                // replace "private const NAME" with "public const NAME"
                $changedFileContent = \str_replace('private const ' . $classConstMatch->getConstantName(), 'public const ' . $classConstMatch->getConstantName(), $classFileContents);
                if ($changedFileContent === $classFileContents) {
                    continue;
                }
                FileSystem::write($classConstMatch->getClassFileName(), $changedFileContent);
                $this->symfonyStyle->note(\sprintf('Updated "%s" constant in "%s" file to public as used outside', $classConstMatch->getConstantName(), $classConstMatch->getClassFileName()));
            }
        }
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
        $fileContent = Strings::replace($fileContents, '#^(    |\\t)const #', '$1private const ');
        return \str_replace('public const ', 'private const ', $fileContent);
    }
    /**
     * @param string[] $paths
     * @return array<string, mixed>
     */
    private function runPHPStanAnalyse(array $paths) : array
    {
        $phpStanAnalyseProcess = new Process(\array_merge(['vendor/bin/phpstan', 'analyse'], $paths, ['--configuration', __DIR__ . '/../../config/privatize-constants-phpstan-ruleset.neon', '--error-format', 'json']));
        $phpStanAnalyseProcess->run();
        $resultOutput = $phpStanAnalyseProcess->getOutput() ?: $phpStanAnalyseProcess->getErrorOutput();
        return \json_decode($resultOutput, \true);
    }
    private function resolveClassConstMatch(string $errorMessage) : ?ClassConstMatch
    {
        if (\strpos($errorMessage, 'Access to private constant') === \false) {
            return null;
        }
        $match = Strings::match($errorMessage, self::CONSTANT_MESSAGE_REGEX);
        if (!isset($match['constant_name'], $match['class_name'])) {
            return null;
        }
        /** @var class-string $className */
        $className = (string) $match['class_name'];
        return new ClassConstMatch($className, (string) $match['constant_name']);
    }
}
