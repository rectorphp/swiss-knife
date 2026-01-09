<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class DumpEditorconfigCommand implements CommandInterface
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
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

    public function run(): int
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
