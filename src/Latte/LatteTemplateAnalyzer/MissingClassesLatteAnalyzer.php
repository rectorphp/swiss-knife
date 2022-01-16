<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Latte\LatteTemplateAnalyzer;

use EasyCI20220116\Nette\Utils\Strings;
use EasyCI20220116\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220116\Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use EasyCI20220116\Symplify\EasyCI\ValueObject\FileError;
use EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassesLatteAnalyzer\MissingClassesLatteAnalyzerTest
 */
final class MissingClassesLatteAnalyzer implements \EasyCI20220116\Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface
{
    /**
     * @see https://regex101.com/r/Wrfff2/7
     * @var string
     */
    private const CLASS_REGEX = '#\\b(?<class>[A-Z][\\w\\\\]+)::#m';
    /**
     * @see https://regex101.com/r/Wrfff2/12
     * @var string
     */
    private const VARTYPE_INSTANCEOF_CLASS_REGEX = '#(vartype|varType|instanceof|instanceOf)\\s+(\\\\)?(?<class>[A-Z][\\w\\\\]+)#ms';
    /**
     * @see https://regex101.com/r/8UK0P4/1
     * @var string
     */
    private const SCRIPT_CONTENTS_REGEX = '#<script(.*?)>(.*?)</script>#ms';
    /**
     * @var \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    public function __construct(\EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array
    {
        $errors = [];
        foreach ($fileInfos as $fileInfo) {
            // clear content from javascript fiels
            $fileContents = \EasyCI20220116\Nette\Utils\Strings::replace($fileInfo->getContents(), self::SCRIPT_CONTENTS_REGEX, '');
            $classMatches = \EasyCI20220116\Nette\Utils\Strings::matchAll($fileContents, self::CLASS_REGEX);
            $varTypeInstanceOfClassMatches = \EasyCI20220116\Nette\Utils\Strings::matchAll($fileContents, self::VARTYPE_INSTANCEOF_CLASS_REGEX);
            $matches = \array_merge($classMatches, $varTypeInstanceOfClassMatches);
            if ($matches === []) {
                continue;
            }
            foreach ($matches as $match) {
                $class = (string) $match['class'];
                if ($this->classLikeExistenceChecker->doesClassLikeExist($class)) {
                    continue;
                }
                $errors[] = new \EasyCI20220116\Symplify\EasyCI\ValueObject\FileError(\sprintf('Class "%s" not found', $class), $fileInfo);
            }
        }
        return $errors;
    }
}
