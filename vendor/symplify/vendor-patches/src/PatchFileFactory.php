<?php

declare (strict_types=1);
namespace EasyCI20220529\Symplify\VendorPatches;

use EasyCI20220529\Nette\Utils\Strings;
use EasyCI20220529\Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;
final class PatchFileFactory
{
    public function createPatchFilePath(\EasyCI20220529\Symplify\VendorPatches\ValueObject\OldAndNewFileInfo $oldAndNewFileInfo, string $vendorDirectory) : string
    {
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();
        $inVendorRelativeFilePath = $newFileInfo->getRelativeFilePathFromDirectory($vendorDirectory);
        $relativeFilePathWithoutSuffix = \EasyCI20220529\Nette\Utils\Strings::lower($inVendorRelativeFilePath);
        $pathFileName = \EasyCI20220529\Nette\Utils\Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';
        return 'patches' . \DIRECTORY_SEPARATOR . $pathFileName;
    }
}
