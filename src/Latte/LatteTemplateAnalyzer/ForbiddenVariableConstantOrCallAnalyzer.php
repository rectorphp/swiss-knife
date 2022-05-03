<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Latte\LatteTemplateAnalyzer;

use EasyCI20220503\Nette\Utils\Strings;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\FileError;
use EasyCI20220503\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\ForbiddenVariableConstantOrCallAnalyzer\ForbiddenVariableConstantOrCallAnalyzerTest
 */
final class ForbiddenVariableConstantOrCallAnalyzer implements \Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface
{
    /**
     * @var string
     */
    private const VARIABLE_PART_KEY = 'variable';
    /**
     * @var string
     */
    private const CONSTANT_OR_METHOD_PART_KEY = 'constant_or_method';
    /**
     * @var string
     * @see https://regex101.com/r/mDzFKI/4
     */
    private const ON_VARIABLE_CALL_REGEX = '#(?<' . self::VARIABLE_PART_KEY . '>\\$[\\w]+)::' . '(?<' . self::CONSTANT_OR_METHOD_PART_KEY . '>[\\w_]+)#m';
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array
    {
        $templateErrors = [];
        foreach ($fileInfos as $fileInfo) {
            $currentTemplateErrors = $this->analyzeFileInfo($fileInfo);
            $templateErrors = \array_merge($templateErrors, $currentTemplateErrors);
        }
        return $templateErrors;
    }
    /**
     * @return FileErrorInterface[]
     */
    private function analyzeFileInfo(\EasyCI20220503\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : array
    {
        $matches = \EasyCI20220503\Nette\Utils\Strings::matchAll($fileInfo->getContents(), self::ON_VARIABLE_CALL_REGEX);
        $templateErrors = [];
        foreach ($matches as $match) {
            $errorMessage = \sprintf('On variable "%s::%s" call/constant fetch is not allowed', (string) $match[self::VARIABLE_PART_KEY], (string) $match[self::CONSTANT_OR_METHOD_PART_KEY]);
            $templateErrors[] = new \Symplify\EasyCI\ValueObject\FileError($errorMessage, $fileInfo);
        }
        return $templateErrors;
    }
}
