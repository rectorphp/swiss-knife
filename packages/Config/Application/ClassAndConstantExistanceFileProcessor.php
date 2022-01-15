<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyCI\Config\Application;

use EasyCI20220115\Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use EasyCI20220115\Symplify\EasyCI\Contract\Application\FileProcessorInterface;
use EasyCI20220115\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220115\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer\NonExistingClassConfigFileAnalyzerTest
 */
final class ClassAndConstantExistanceFileProcessor implements \EasyCI20220115\Symplify\EasyCI\Contract\Application\FileProcessorInterface
{
    /**
     * @var ConfigFileAnalyzerInterface[]
     */
    private $configFileAnalyzers;
    /**
     * @param ConfigFileAnalyzerInterface[] $configFileAnalyzers
     */
    public function __construct(array $configFileAnalyzers)
    {
        $this->configFileAnalyzers = $configFileAnalyzers;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos) : array
    {
        $fileErrors = [];
        foreach ($this->configFileAnalyzers as $configFileAnalyzer) {
            $currentFileErrors = $configFileAnalyzer->processFileInfos($fileInfos);
            $fileErrors = \array_merge($fileErrors, $currentFileErrors);
        }
        return $fileErrors;
    }
}
