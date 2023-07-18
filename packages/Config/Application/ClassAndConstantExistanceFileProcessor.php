<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config\Application;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer\NonExistingClassConfigFileAnalyzerTest
 */
final class ClassAndConstantExistanceFileProcessor
{
    /**
     * @var ConfigFileAnalyzerInterface[]
     */
    private readonly array $configFileAnalyzers;

    /**
     * @param RewindableGenerator<int, ConfigFileAnalyzerInterface> $configFileAnalyzers
     */
    public function __construct(iterable $configFileAnalyzers)
    {
        $this->configFileAnalyzers = iterator_to_array($configFileAnalyzers->getIterator());
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($this->configFileAnalyzers as $configFileAnalyzer) {
            $currentFileErrors = $configFileAnalyzer->processFileInfos($fileInfos);
            $fileErrors = array_merge($fileErrors, $currentFileErrors);
        }

        return $fileErrors;
    }
}
