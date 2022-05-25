<?php

declare (strict_types=1);
namespace EasyCI20220525\Symplify\VendorPatches\Composer;

use EasyCI20220525\Symplify\SmartFileSystem\FileSystemGuard;
use EasyCI20220525\Symplify\SmartFileSystem\Json\JsonFileSystem;
use EasyCI20220525\Symplify\SmartFileSystem\SmartFileInfo;
use EasyCI20220525\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use EasyCI20220525\Symplify\VendorPatches\FileSystem\PathResolver;
/**
 * @see \Symplify\VendorPatches\Tests\Composer\PackageNameResolverTest
 */
final class PackageNameResolver
{
    /**
     * @var \Symplify\SmartFileSystem\Json\JsonFileSystem
     */
    private $jsonFileSystem;
    /**
     * @var \Symplify\VendorPatches\FileSystem\PathResolver
     */
    private $pathResolver;
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    private $fileSystemGuard;
    public function __construct(\EasyCI20220525\Symplify\SmartFileSystem\Json\JsonFileSystem $jsonFileSystem, \EasyCI20220525\Symplify\VendorPatches\FileSystem\PathResolver $pathResolver, \EasyCI20220525\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard)
    {
        $this->jsonFileSystem = $jsonFileSystem;
        $this->pathResolver = $pathResolver;
        $this->fileSystemGuard = $fileSystemGuard;
    }
    public function resolveFromFileInfo(\EasyCI20220525\Symplify\SmartFileSystem\SmartFileInfo $vendorFile) : string
    {
        $packageComposerJsonFilePath = $this->getPackageComposerJsonFilePath($vendorFile);
        $composerJson = $this->jsonFileSystem->loadFilePathToJson($packageComposerJsonFilePath);
        if (!isset($composerJson['name'])) {
            throw new \EasyCI20220525\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $composerJson['name'];
    }
    private function getPackageComposerJsonFilePath(\EasyCI20220525\Symplify\SmartFileSystem\SmartFileInfo $vendorFileInfo) : string
    {
        $vendorPackageDirectory = $this->pathResolver->resolveVendorDirectory($vendorFileInfo);
        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        $this->fileSystemGuard->ensureFileExists($packageComposerJsonFilePath, __METHOD__);
        return $packageComposerJsonFilePath;
    }
}
