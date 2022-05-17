<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\Finder;

use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use EasyCI20220517\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassNamesFinder
{
    /**
     * @var \Symplify\EasyCI\ActiveClass\ClassNameResolver
     */
    private $classNameResolver;
    public function __construct(\Symplify\EasyCI\ActiveClass\ClassNameResolver $classNameResolver)
    {
        $this->classNameResolver = $classNameResolver;
    }
    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $phpFileInfos) : array
    {
        $filesWithClasses = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $className = $this->classNameResolver->resolveFromFromFileInfo($phpFileInfo);
            if ($className === null) {
                continue;
            }
            $filesWithClasses[] = new \Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass($phpFileInfo, $className);
        }
        return $filesWithClasses;
    }
}
