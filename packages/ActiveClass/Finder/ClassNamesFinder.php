<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Finder;

use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassNamesFinder
{
    public function __construct(
        private readonly ClassNameResolver $classNameResolver,
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths): array
    {
        $filesWithClasses = [];

        foreach ($filePaths as $filePath) {
            $className = $this->classNameResolver->resolveFromFromFilePath($filePath);
            if ($className === null) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass($filePath, $className);
        }

        return $filesWithClasses;
    }
}
