<?php

declare (strict_types=1);
namespace SwissKnife202606\TomasVotruba\ClassLeak\Reporting;

use SwissKnife202606\TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use SwissKnife202606\TomasVotruba\ClassLeak\ValueObject\UnusedClassesResult;
final class UnusedClassesResultFactory
{
    /**
     * @param FileWithClass[] $unusedFilesWithClasses
     */
    public function create(array $unusedFilesWithClasses) : UnusedClassesResult
    {
        $parentLessFileWithClasses = [];
        $withParentsFileWithClasses = [];
        $traits = [];
        foreach ($unusedFilesWithClasses as $unusedFileWithClass) {
            if ($unusedFileWithClass->hasParentClassOrInterface()) {
                $withParentsFileWithClasses[] = $unusedFileWithClass;
            } elseif ($unusedFileWithClass->isTrait()) {
                $traits[] = $unusedFileWithClass;
            } else {
                $parentLessFileWithClasses[] = $unusedFileWithClass;
            }
        }
        return new UnusedClassesResult($parentLessFileWithClasses, $withParentsFileWithClasses, $traits);
    }
}
