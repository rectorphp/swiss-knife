<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Template;

use EasyCI20220306\Nette\Utils\Strings;
use EasyCI20220306\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI20220306\Symplify\SmartFileSystem\SmartFileInfo;
use EasyCI20220306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TemplatePathsResolver
{
    /**
     * @see https://regex101.com/r/dAH2eR/1
     * @var string
     */
    private const TEMPLATE_PATH_REGEX = '#(views|template)\\/(?<template_relative_path>.*?)$#';
    /**
     * @see https://regex101.com/r/1xa9Ey/1
     * @var string
     */
    private const BUNDLE_NAME_REGEX = '#\\/(?<bundle_name>[\\w]+)Bundle\\.php$#';
    /**
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    private $smartFinder;
    public function __construct(\EasyCI20220306\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder)
    {
        $this->smartFinder = $smartFinder;
    }
    /**
     * @param string[] $directories
     * @return string[]
     */
    public function resolveFromDirectories(array $directories) : array
    {
        $twigTemplateFileInfos = $this->smartFinder->find($directories, '*.twig');
        return $this->resolveTemplatePathsWithBundle($twigTemplateFileInfos);
    }
    /**
     * @param SmartFileInfo[] $twigTemplateFileInfos
     * @return string[]
     */
    private function resolveTemplatePathsWithBundle(array $twigTemplateFileInfos) : array
    {
        $templatePathsWithBundle = [];
        foreach ($twigTemplateFileInfos as $twigTemplateFileInfo) {
            $relativeTemplateFilepath = $this->resolveRelativeTemplateFilepath($twigTemplateFileInfo);
            $bundlePrefix = $this->findBundlePrefix($twigTemplateFileInfo);
            $templatePathsWithBundle[] = '@' . $bundlePrefix . '/' . $relativeTemplateFilepath;
        }
        \sort($templatePathsWithBundle);
        return $templatePathsWithBundle;
    }
    private function findBundlePrefix(\EasyCI20220306\Symplify\SmartFileSystem\SmartFileInfo $templateFileInfo) : string
    {
        $templateRealPath = $templateFileInfo->getRealPath();
        $bundleFileInfo = null;
        $currentDirectory = \dirname($templateRealPath);
        do {
            /** @var string[] $foundFiles */
            $foundFiles = \glob($currentDirectory . '/*Bundle.php');
            if ($foundFiles !== []) {
                $bundleFileRealPath = $foundFiles[0];
                $match = \EasyCI20220306\Nette\Utils\Strings::match($bundleFileRealPath, self::BUNDLE_NAME_REGEX);
                if (!isset($match['bundle_name'])) {
                    throw new \EasyCI20220306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
                }
                return $match['bundle_name'];
            }
            $currentDirectory = \dirname($currentDirectory);
            // root dir, stop!
            if ($currentDirectory === '/') {
                break;
            }
        } while ($bundleFileInfo === null);
        throw new \EasyCI20220306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    private function resolveRelativeTemplateFilepath(\EasyCI20220306\Symplify\SmartFileSystem\SmartFileInfo $templateFileInfo) : string
    {
        $match = \EasyCI20220306\Nette\Utils\Strings::match($templateFileInfo->getRealPath(), self::TEMPLATE_PATH_REGEX);
        if (!isset($match['template_relative_path'])) {
            throw new \EasyCI20220306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $match['template_relative_path'];
    }
}
