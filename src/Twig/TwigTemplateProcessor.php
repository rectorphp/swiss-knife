<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Twig;

use EasyCI20220607\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220607\Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use EasyCI20220607\Symplify\SmartFileSystem\SmartFileInfo;
final class TwigTemplateProcessor
{
    /**
     * @var TwigTemplateAnalyzerInterface[]
     */
    private $twigTemplateAnalyzers;
    /**
     * @param TwigTemplateAnalyzerInterface[] $twigTemplateAnalyzers
     */
    public function __construct(array $twigTemplateAnalyzers)
    {
        $this->twigTemplateAnalyzers = $twigTemplateAnalyzers;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos) : array
    {
        $templateErrors = [];
        foreach ($this->twigTemplateAnalyzers as $twigTemplateAnalyzer) {
            $currentTemplateErrors = $twigTemplateAnalyzer->analyze($fileInfos);
            $templateErrors = \array_merge($templateErrors, $currentTemplateErrors);
        }
        return $templateErrors;
    }
}
