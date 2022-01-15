<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyCI\ActiveClass\Finder;

use EasyCI20220115\Symplify\EasyCI\ActiveClass\ClassNameResolver;
use EasyCI20220115\Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use EasyCI20220115\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassNamesFinder
{
    /**
     * @var \Symplify\EasyCI\ActiveClass\ClassNameResolver
     */
    private $classNameResolver;
    public function __construct(\EasyCI20220115\Symplify\EasyCI\ActiveClass\ClassNameResolver $classNameResolver)
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
            $filesWithClasses[] = new \EasyCI20220115\Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass($phpFileInfo, $className);
        }
        return $filesWithClasses;
    }
}
