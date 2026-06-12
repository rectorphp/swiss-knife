<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
use SwissKnife202606\Nette\Utils\FileSystem;
final class DumpEditorconfigCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
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
            $this->outputPrinter->error('.editorconfig file already exists');
            return ExitCode::ERROR;
        }
        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);
        $this->outputPrinter->success('.editorconfig file was created');
        return ExitCode::SUCCESS;
    }
}
