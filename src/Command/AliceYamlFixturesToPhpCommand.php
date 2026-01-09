<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use PhpParser\BuilderHelpers;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Rector\SwissKnife\Finder\FilesFinder;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

/**
 * @see https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php
 */
final class AliceYamlFixturesToPhpCommand implements \Entropy\Console\Contract\CommandInterface
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    /**
     * @param string[] $sources One or more paths to check
     * @return ExitCode::*
     */
    public function run(array $sources): int
    {
        $yamlFileInfos = FilesFinder::findYamlFiles($sources);

        $standard = new Standard();

        // use php-parser to dump their PHP version, @see https://github.com/nelmio/alice/blob/main/doc%2Fcomplete-reference.md#php
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $yaml = Yaml::parseFile($yamlFileInfo->getRealPath());

            $return = $this->createArrayReturn($yaml);
            $phpFileContents = $standard->prettyPrintFile([$return]);

            // get real path without yml/yaml suffix
            if (str_ends_with($yamlFileInfo->getRealPath(), '.yml')) {
                $phpFilePath = substr($yamlFileInfo->getRealPath(), 0, -4) . '.php';
            } else {
                $phpFilePath = substr($yamlFileInfo->getRealPath(), 0, -5) . '.php';
            }

            FileSystem::write($phpFilePath, $phpFileContents, null);

            // remove YAML file
            unlink($yamlFileInfo->getRealPath());

            $this->symfonyStyle->writeln('[DELETED] ' . $yamlFileInfo->getRelativePathname());
            $this->symfonyStyle->writeln('[ADDED] ' . $phpFilePath);
            $this->symfonyStyle->newLine();
        }

        $this->symfonyStyle->success(
            sprintf('Successfully converted %d Alice YAML fixtures to PHP', count($yamlFileInfos))
        );

        return \Entropy\Console\Enum\ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'alice-yaml-fixtures-to-php';
    }

    public function getDescription(): string
    {
        return 'Converts Alice YAML fixtures to PHP format, so Rector and PHPStan can understand it';
    }

    /**
     * @param mixed[] $yaml
     */
    private function createArrayReturn(array $yaml): Return_
    {
        $expr = BuilderHelpers::normalizeValue($yaml);

        return new Return_($expr);
    }
}
