<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220211\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220211\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220211\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Git\ConflictResolver;
use EasyCI20220211\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220211\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220211\Symplify\PackageBuilder\ValueObject\Option;
final class CheckConflictsCommand extends \EasyCI20220211\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Git\ConflictResolver
     */
    private $conflictResolver;
    public function __construct(\Symplify\EasyCI\Git\ConflictResolver $conflictResolver)
    {
        $this->conflictResolver = $conflictResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220211\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument(\EasyCI20220211\Symplify\PackageBuilder\ValueObject\Option::SOURCES, \EasyCI20220211\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220211\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to project');
    }
    protected function execute(\EasyCI20220211\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220211\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(\EasyCI20220211\Symplify\PackageBuilder\ValueObject\Option::SOURCES);
        $fileInfos = $this->smartFinder->find($source, '*', ['vendor']);
        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($fileInfos);
        if ($conflictsCountByFilePath === []) {
            $message = \sprintf('No conflicts found in %d files', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
        }
        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = \sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }
        return self::FAILURE;
    }
}
