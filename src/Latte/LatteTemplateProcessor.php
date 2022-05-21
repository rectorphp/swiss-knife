<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Latte;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use EasyCI20220521\Symplify\SmartFileSystem\SmartFileInfo;
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
        $TemplateErrors = [];
        foreach ($this->latteAnalyzers as $latteAnalyzer) {
            $currentTemplateErrors = $latteAnalyzer->analyze($fileInfos);
            $TemplateErrors = \array_merge($TemplateErrors, $currentTemplateErrors);
        }
        return $TemplateErrors;
    }
}
