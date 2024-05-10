<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Rector\SwissKnife\Finder\FilesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PrettyJsonCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('pretty-json');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'File or directory to prettify'
        );
        $this->setDescription('Turns JSON files from 1-line format to pretty format');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $jsonFileInfos = FilesFinder::findJsonFiles($sources);

        if (count($jsonFileInfos) === 0) {
            $this->symfonyStyle->error('No *.json files found');
            return self::FAILURE;
        }

        $message = sprintf('Analysing %d *.json files', count($jsonFileInfos));
        $this->symfonyStyle->note($message);

        // convert file infos from uggly json to pretty json
        foreach ($jsonFileInfos as $jsonFileInfo) {
            $jsonContent = FileSystem::read($jsonFileInfo->getRealPath());
            $prettyJsonContent = Json::encode(Json::decode($jsonContent), JSON_PRETTY_PRINT);

            // nothing to convert
            if ($prettyJsonContent === $jsonContent) {
                continue;
            }

            // notify the file was changed
            $this->symfonyStyle->writeln(
                sprintf('The "%s" file was changed to pretty json', $jsonFileInfo->getRealPath())
            );
            FileSystem::write($jsonFileInfo->getRealPath(), $prettyJsonContent);
        }

        return self::SUCCESS;
    }
}
