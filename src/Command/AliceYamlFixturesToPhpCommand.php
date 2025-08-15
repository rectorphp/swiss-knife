<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202508\Nette\Utils\FileSystem;
use SwissKnife202508\PhpParser\BuilderHelpers;
use SwissKnife202508\PhpParser\Node\Stmt\Return_;
use SwissKnife202508\PhpParser\PrettyPrinter\Standard;
use Rector\SwissKnife\Finder\FilesFinder;
use SwissKnife202508\Symfony\Component\Console\Command\Command;
use SwissKnife202508\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202508\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202508\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202508\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202508\Symfony\Component\Yaml\Yaml;
/**
 * @see https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php
 */
final class AliceYamlFixturesToPhpCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('alice-yaml-fixtures-to-php');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check');
        $this->setDescription('Converts Alice YAML fixtures to PHP format, so Rector and PHPStan can understand it');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
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
            FileSystem::write($phpFilePath, $phpFileContents);
            // remove YAML file
            \unlink($yamlFileInfo->getRealPath());
            $this->symfonyStyle->writeln('[DELETED] ' . $yamlFileInfo->getRelativePathname());
            $this->symfonyStyle->writeln('[ADDED] ' . $phpFilePath);
            $this->symfonyStyle->newLine();
        }
        $this->symfonyStyle->success(\sprintf('Successfully converted %d Alice YAML fixtures to PHP', \count($yamlFileInfos)));
        return self::SUCCESS;
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
