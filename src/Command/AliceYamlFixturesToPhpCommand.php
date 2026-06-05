<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Nette\Utils\FileSystem;
use SwissKnife202606\PhpParser\BuilderHelpers;
use SwissKnife202606\PhpParser\Node\Stmt\Return_;
use SwissKnife202606\PhpParser\PrettyPrinter\Standard;
use Rector\SwissKnife\Finder\FilesFinder;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202606\Symfony\Component\Yaml\Yaml;
/**
 * @see https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php
 */
final class AliceYamlFixturesToPhpCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $sources One or more paths to check
     * @return ExitCode::*
     */
    public function run(array $sources) : int
    {
        $yamlFileInfos = FilesFinder::findYamlFiles($sources);
        $standard = new Standard();
        // use php-parser to dump their PHP version, @see https://github.com/nelmio/alice/blob/main/doc%2Fcomplete-reference.md#php
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $yaml = Yaml::parseFile($yamlFileInfo->getRealPath());
            $return = $this->createArrayReturn($yaml);
            $phpFileContents = $standard->prettyPrintFile([$return]);
            // get real path without yml/yaml suffix
            if (\substr_compare($yamlFileInfo->getRealPath(), '.yml', -\strlen('.yml')) === 0) {
                $phpFilePath = \substr($yamlFileInfo->getRealPath(), 0, -4) . '.php';
            } else {
                $phpFilePath = \substr($yamlFileInfo->getRealPath(), 0, -5) . '.php';
            }
            FileSystem::write($phpFilePath, $phpFileContents, null);
            // remove YAML file
            \unlink($yamlFileInfo->getRealPath());
            $this->symfonyStyle->writeln('[DELETED] ' . $yamlFileInfo->getRelativePathname());
            $this->symfonyStyle->writeln('[ADDED] ' . $phpFilePath);
            $this->symfonyStyle->newLine();
        }
        $this->symfonyStyle->success(\sprintf('Successfully converted %d Alice YAML fixtures to PHP', \count($yamlFileInfos)));
        return ExitCode::SUCCESS;
    }
    public function getName() : string
    {
        return 'alice-yaml-fixtures-to-php';
    }
    public function getDescription() : string
    {
        return 'Converts Alice YAML fixtures to PHP format, so Rector and PHPStan can understand it';
    }
    /**
     * @param mixed[] $yaml
     */
    private function createArrayReturn(array $yaml) : Return_
    {
        $expr = BuilderHelpers::normalizeValue($yaml);
        return new Return_($expr);
    }
}
