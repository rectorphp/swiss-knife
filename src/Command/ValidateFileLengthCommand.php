<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Finder\FilesFinder;
use Rector\SwissKnife\Resolver\TooLongFilesResolver;
use SwissKnife202402\Symfony\Component\Console\Command\Command;
use SwissKnife202402\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202402\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202402\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202402\Symfony\Component\Console\Style\SymfonyStyle;
final class ValidateFileLengthCommand extends Command
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Finder\FilesFinder
     */
    private $projectFilesFinder;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Resolver\TooLongFilesResolver
     */
    private $tooLongFilesResolver;
    public function __construct(FilesFinder $projectFilesFinder, SymfonyStyle $symfonyStyle, TooLongFilesResolver $tooLongFilesResolver)
    {
        $this->projectFilesFinder = $projectFilesFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->tooLongFilesResolver = $tooLongFilesResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('validate-file-length');
        $this->setDescription('Make sure the file path length are not breaking normal Windows max length');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument('sources');
        $fileInfos = $this->projectFilesFinder->find($sources);
        $tooLongFileInfos = $this->tooLongFilesResolver->resolve($fileInfos);
        if ($tooLongFileInfos === []) {
            $message = \sprintf('Checked %d files - all fit max file length', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
        }
        foreach ($tooLongFileInfos as $tooLongFileInfo) {
            $message = \sprintf('Paths for file "%s" has %d chars, but must be shorter than %d.', $tooLongFileInfo->getRealPath(), \strlen($tooLongFileInfo->getRealPath()), TooLongFilesResolver::MAX_FILE_LENGTH);
            $this->symfonyStyle->warning($message);
        }
        return self::FAILURE;
    }
}
