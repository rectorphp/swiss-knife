<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220117\Symfony\Component\Console\Command\Command;
use EasyCI20220117\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220117\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220117\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220117\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Finder\ProjectFilesFinder;
use Symplify\EasyCI\Resolver\TooLongFilesResolver;
use EasyCI20220117\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220117\Symplify\PackageBuilder\ValueObject\Option;
final class ValidateFileLengthCommand extends \EasyCI20220117\Symfony\Component\Console\Command\Command
{
    /**
     * @var \Symplify\EasyCI\Finder\ProjectFilesFinder
     */
    private $projectFilesFinder;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\EasyCI\Resolver\TooLongFilesResolver
     */
    private $tooLongFilesResolver;
    public function __construct(\Symplify\EasyCI\Finder\ProjectFilesFinder $projectFilesFinder, \EasyCI20220117\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \Symplify\EasyCI\Resolver\TooLongFilesResolver $tooLongFilesResolver)
    {
        $this->projectFilesFinder = $projectFilesFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->tooLongFilesResolver = $tooLongFilesResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220117\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('[CI] Make sure the file path length are not breaking normal Windows max length');
        $this->addArgument(\EasyCI20220117\Symplify\PackageBuilder\ValueObject\Option::SOURCES, \EasyCI20220117\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220117\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to project');
    }
    protected function execute(\EasyCI20220117\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220117\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(\EasyCI20220117\Symplify\PackageBuilder\ValueObject\Option::SOURCES);
        $fileInfos = $this->projectFilesFinder->find($sources);
        $tooLongFileInfos = $this->tooLongFilesResolver->resolve($fileInfos);
        if ($tooLongFileInfos === []) {
            $message = \sprintf('Checked %d files - all fit max file length', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
        }
        foreach ($tooLongFileInfos as $tooLongFileInfo) {
            $message = \sprintf('Paths for file "%s" has %d chars, but must be shorter than %d.', $tooLongFileInfo->getRealPath(), \strlen($tooLongFileInfo->getRealPath()), \Symplify\EasyCI\Resolver\TooLongFilesResolver::MAX_FILE_LENGTH);
            $this->symfonyStyle->warning($message);
        }
        return self::FAILURE;
    }
}
