<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220302\Nette\Utils\Json;
use EasyCI20220302\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220302\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220302\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Composer\SupportedPhpVersionResolver;
use Symplify\EasyCI\Exception\ShouldNotHappenException;
use EasyCI20220302\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220302\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class PhpVersionsJsonCommand extends \EasyCI20220302\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE_PATH = 'composer_json_file_path';
    /**
     * @var \Symplify\EasyCI\Composer\SupportedPhpVersionResolver
     */
    private $supportedPhpVersionResolver;
    public function __construct(\Symplify\EasyCI\Composer\SupportedPhpVersionResolver $supportedPhpVersionResolver)
    {
        $this->supportedPhpVersionResolver = $supportedPhpVersionResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220302\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(self::COMPOSER_JSON_FILE_PATH, \EasyCI20220302\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Path to composer.json', \getcwd() . '/composer.json');
        $this->setDescription('Generate supported PHP versions based on `composer.json` in JSON format. Useful for PHP matrix build in CI');
    }
    protected function execute(\EasyCI20220302\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220302\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $composerJsonFilePath = (string) $input->getArgument(self::COMPOSER_JSON_FILE_PATH);
        $this->fileSystemGuard->ensureFileExists($composerJsonFilePath, __METHOD__);
        $supportedPhpVersions = $this->supportedPhpVersionResolver->resolveFromComposerJsonFilePath($composerJsonFilePath);
        if ($supportedPhpVersions === []) {
            $message = \sprintf('No PHP versions were resolved from "%s"', $composerJsonFilePath);
            throw new \Symplify\EasyCI\Exception\ShouldNotHappenException($message);
        }
        // output must be without spaces, otherwise it breaks the GitHub Actions json
        $jsonContent = \EasyCI20220302\Nette\Utils\Json::encode($supportedPhpVersions);
        $this->symfonyStyle->writeln($jsonContent);
        return self::SUCCESS;
    }
}
