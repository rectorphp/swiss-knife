<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Latte;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use EasyCI202207\Symplify\SmartFileSystem\SmartFileInfo;
final class LatteTemplateProcessor
{
    /**
     * @var LatteTemplateAnalyzerInterface[]
     */
    private $latteAnalyzers;
    /**
     * @param LatteTemplateAnalyzerInterface[] $latteAnalyzers
     */
    public function __construct(array $latteAnalyzers)
    {
        $this->latteAnalyzers = $latteAnalyzers;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos) : array
    {
        $templateErrors = [];
        foreach ($this->latteAnalyzers as $latteAnalyzer) {
            $currentTemplateErrors = $latteAnalyzer->analyze($fileInfos);
            $templateErrors = \array_merge($templateErrors, $currentTemplateErrors);
        }
        return $templateErrors;
    }
}
