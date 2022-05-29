<?php

declare (strict_types=1);
namespace EasyCI20220529\Symplify\PackageBuilder\Console\Command;

use EasyCI20220529\Symfony\Component\Console\Command\Command;
use EasyCI20220529\Symfony\Component\Console\Input\InputOption;
use EasyCI20220529\Symfony\Component\Console\Style\SymfonyStyle;
use EasyCI20220529\Symfony\Contracts\Service\Attribute\Required;
use EasyCI20220529\Symplify\PackageBuilder\ValueObject\Option;
use EasyCI20220529\Symplify\SmartFileSystem\FileSystemGuard;
use EasyCI20220529\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI20220529\Symplify\SmartFileSystem\SmartFileSystem;
abstract class AbstractSymplifyCommand extends \EasyCI20220529\Symfony\Component\Console\Command\Command
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $symfonyStyle;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    protected $smartFileSystem;
    /**
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    protected $smartFinder;
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    protected $fileSystemGuard;
    public function __construct()
    {
        parent::__construct();
        $this->addOption(\EasyCI20220529\Symplify\PackageBuilder\ValueObject\Option::CONFIG, 'c', \EasyCI20220529\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Path to config file');
    }
    /**
     * @required
     */
    public function autowire(\EasyCI20220529\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \EasyCI20220529\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \EasyCI20220529\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder, \EasyCI20220529\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard) : void
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
