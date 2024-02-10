<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Command;

use EasyCI202402\Nette\Utils\FileSystem;
use EasyCI202402\Symfony\Component\Console\Command\Command;
use EasyCI202402\Symfony\Component\Console\Input\InputInterface;
use EasyCI202402\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
final class DumpEditorconfigCommand extends Command
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
        $this->setName('dump-editorconfig');
        $this->setDescription('Dump .editorconfig file to project root');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $projectEditorconfigFilePath = \getcwd() . '/.editorconfig';
        if (\file_exists($projectEditorconfigFilePath)) {
            $this->symfonyStyle->error('.editorconfig file already exists');
            return self::FAILURE;
        }
        FileSystem::copy(__DIR__ . '/../../templates/.editorconfig', $projectEditorconfigFilePath);
        $this->symfonyStyle->success('.editorconfig file was created');
        return self::SUCCESS;
    }
}