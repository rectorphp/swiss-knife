<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Testing\Printer;

use Rector\SwissKnife\FileSystem\PathHelper;
final class PHPUnitXmlPrinter
{
    /**
     * Prints lists of <file> elements in https://phpunit.readthedocs.io/en/9.5/configuration.html#the-testsuite-element
     *
     * @param string[] $filePaths
     */
    public function printFiles(array $filePaths) : string
    {
        $fileContents = '';
        foreach ($filePaths as $filePath) {
            $relativeFilePath = PathHelper::relativeToCwd($filePath);
            $fileContents .= '<file>' . $relativeFilePath . '</file>' . \PHP_EOL;
        }
        return $fileContents;
    }
}
