<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202402\Nette\Utils\Json;
use SwissKnife202402\Symfony\Component\Console\Command\Command;
use SwissKnife202402\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202402\Symfony\Component\Console\Input\InputOption;
use SwissKnife202402\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202402\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202402\Symfony\Component\Process\Process;
use SwissKnife202402\Symfony\Component\Stopwatch\Stopwatch;
use SwissKnife202402\Webmozart\Assert\Assert;
final class SpeedRunToolCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var array<array<mixed|string>>
     */
    private $collectedData = [];
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('speed-run-tool');
        $this->setDescription('Test speed tool run, e.g. PHPStan or Rector, in various versions');
        $this->addArgument('package-name', InputOption::VALUE_REQUIRED, 'Name of package');
        $this->addOption('script-name', null, InputOption::VALUE_REQUIRED, 'Name of composer script to run');
        $this->addOption('run-count', null, InputOption::VALUE_REQUIRED, 'Number of runs', 3);
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $packageName = (string) $input->getArgument('package-name');
        $composerScriptName = (string) $input->getOption('script-name');
        $numberOfRuns = (int) $input->getOption('run-count');
        $versionsToTry = $this->resolveVersionsToTry($packageName, $numberOfRuns);
        $i = 1;
        foreach ($versionsToTry as $versionToTry) {
            $this->symfonyStyle->title(\sprintf('%d of %d) Running "composer %s" with version "%s:%s"', $i, $numberOfRuns, $composerScriptName, $packageName, $versionToTry));
            $requireVersionProcess = new Process(['composer', 'require', $packageName . ':^' . $versionToTry]);
            $requireVersionProcess->mustRun();
            $this->symfonyStyle->note(\sprintf('Composer require of "%s" version finished', $versionToTry));
            $stopwatch = new Stopwatch();
            $stopwatchEvent = $stopwatch->start('script');
            $scriptProcess = new Process(['composer', $composerScriptName]);
            $scriptProcess->run();
            $result = $stopwatchEvent->stop();
            $this->collectedData[] = ['version' => $versionToTry, 'duration' => \sprintf('%.2f s', $result->getDuration() / 1000), 'memory' => \sprintf('%d MB', $result->getMemory() / 1024 / 1024)];
            $this->symfonyStyle->success('Script run finished');
            $this->symfonyStyle->newLine();
            ++$i;
        }
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->table(['Version', 'Time', 'Memory'], $this->collectedData);
        return self::SUCCESS;
    }
    /**
     * @return string[]
     */
    private function resolveVersionsToTry(string $packageName, int $numberOfRuns) : array
    {
        $versionsToTry = [];
        $packagistUrl = 'https://repo.packagist.org/p2/' . $packageName . '.json';
        $packagistJson = \file_get_contents($packagistUrl);
        Assert::string($packagistJson);
        $versionDatas = Json::decode($packagistJson, Json::FORCE_ARRAY)['packages'][$packageName];
        foreach ($versionDatas as $versionData) {
            $versionsToTry[] = $versionData['version'];
        }
        return \array_slice($versionsToTry, 0, $numberOfRuns);
    }
}
