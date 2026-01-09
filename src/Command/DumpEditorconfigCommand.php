<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DumpEditorconfigCommand implements CommandInterface
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    public function getName(): string
    {
        return 'dump-editorconfig';
    }

    public function getDescription(): string
    {
        return 'Dump .editorconfig file to project root';
    }

    private function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectEditorconfigFilePath = getcwd() . '/.editorconfig';
        if (file_exists($projectEditorconfigFilePath)) {
            $this->symfonyStyle->error('.editorconfig file already exists');

            return ExitCode::ERROR;
        }

        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);

        $this->symfonyStyle->success('.editorconfig file was created');

        return ExitCode::SUCCESS;
    }
}
