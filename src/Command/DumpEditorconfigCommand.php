<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Nette\Utils\FileSystem;

final readonly class DumpEditorconfigCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
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
            $this->outputPrinter->error('.editorconfig file already exists');

            return ExitCode::ERROR;
        }

        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);

        $this->outputPrinter->success('.editorconfig file was created');

        return ExitCode::SUCCESS;
    }
}
