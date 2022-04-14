<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Configuration;

use EasyCI20220414\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCI\Psr4\Exception\ConfigurationException;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use EasyCI20220414\Symplify\SmartFileSystem\FileSystemGuard;
final class Psr4SwitcherConfiguration
{
    /**
     * @var string[]
     */
    private $source = [];
    /**
     * @var string|null
     */
    private $composerJsonPath;
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    private $fileSystemGuard;
    public function __construct(\EasyCI20220414\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard)
    {
        $this->fileSystemGuard = $fileSystemGuard;
    }
    /**
     * For testing
     */
    public function loadForTest(string $composerJsonPath) : void
    {
        $this->composerJsonPath = $composerJsonPath;
    }
    public function loadFromInput(\EasyCI20220414\Symfony\Component\Console\Input\InputInterface $input) : void
    {
        $composerJsonPath = (string) $input->getOption(\Symplify\EasyCI\Psr4\ValueObject\Option::COMPOSER_JSON);
        if ($composerJsonPath === '') {
            throw new \Symplify\EasyCI\Psr4\Exception\ConfigurationException(\sprintf('Provide composer.json via "--%s"', \Symplify\EasyCI\Psr4\ValueObject\Option::COMPOSER_JSON));
        }
        $this->fileSystemGuard->ensureFileExists($composerJsonPath, __METHOD__);
        $this->composerJsonPath = $composerJsonPath;
        $this->source = (array) $input->getArgument(\Symplify\EasyCI\Psr4\ValueObject\Option::SOURCES);
    }
    /**
     * @return string[]
     */
    public function getSource() : array
    {
        return $this->source;
    }
    public function getComposerJsonPath() : string
    {
        if ($this->composerJsonPath === null) {
            throw new \Symplify\EasyCI\Psr4\Exception\ConfigurationException();
        }
        return $this->composerJsonPath;
    }
}
