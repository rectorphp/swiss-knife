<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Nette\Utils\FileSystem;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
final class DumpEditorconfigCommand implements CommandInterface
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
    public function getName() : string
    {
        return 'dump-editorconfig';
    }
    public function getDescription() : string
    {
        return 'Dump .editorconfig file to project root';
    }
    public function run() : int
    {
        $projectEditorconfigFilePath = \getcwd() . '/.editorconfig';
        if (\file_exists($projectEditorconfigFilePath)) {
            $this->symfonyStyle->error('.editorconfig file already exists');
            return ExitCode::ERROR;
        }
        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);
        $this->symfonyStyle->success('.editorconfig file was created');
        return ExitCode::SUCCESS;
    }
}
