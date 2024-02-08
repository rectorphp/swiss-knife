<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DumpEditorconfigCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('dump-editorconfig');
        $this->setDescription('Dump .editorconfig file to project root');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectEditorconfigFilePath = getcwd() . '/.editorconfig';
        if (file_exists($projectEditorconfigFilePath)) {
            $this->symfonyStyle->error('.editorconfig file already exists');
            return self::FAILURE;
        }

        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);
        $this->symfonyStyle->success('.editorconfig file was created');

        return self::SUCCESS;
    }
}
