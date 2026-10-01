<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Composer;

use Entropy\Utils\FileSystem;
use Symfony\Component\Process\Process;

final class ComposerOutdatedResponseProvider
{
    /**
     * @var int
     */
    private const WEEK_IN_SECONDS = 60 * 60 * 24 * 7;

    public function provide(): string
    {
        $composerOutdatedFilePath = $this->resolveComposerOutdatedFilePath();

        // let's use cache
        if ($this->shouldLoadCacheFile($composerOutdatedFilePath)) {
            /** @var string $composerOutdatedFilePath */
            return FileSystem::read($composerOutdatedFilePath);
        }

        $composerOutdatedProcess = Process::fromShellCommandline(
            'composer outdated --direct --minor-only --format json --ignore-platform-req=php',
            null,
            null,
            null,
            120
        );

        $composerOutdatedProcess->mustRun();

        $processResult = $composerOutdatedProcess->getOutput();

        if (is_string($composerOutdatedFilePath)) {
            FileSystem::ensureDirectoryExists(dirname($composerOutdatedFilePath));
            FileSystem::write($composerOutdatedFilePath, $processResult);
        }

        return $processResult;
    }

    private function resolveProjectComposerHash(): ?string
    {
        if (file_exists(getcwd() . '/composer.lock')) {
            return (string) md5_file(getcwd() . '/composer.lock');
        }

        if (file_exists(getcwd() . '/composer.json')) {
            return (string) md5_file(getcwd() . '/composer.json');
        }

        return null;
    }

    private function resolveComposerOutdatedFilePath(): ?string
    {
        $projectComposerHash = $this->resolveProjectComposerHash();
        if (! in_array($projectComposerHash, [null, ''], true)) {
            // load from cache if we already made the analysis
            return sys_get_temp_dir() . '/jack/composer-outdated-' . $projectComposerHash . '.json';
        }

        return null;
    }

    private function isFileYoungerThanWeek(string $filePath): bool
    {
        $fileTime = filemtime($filePath);
        if ($fileTime === false) {
            return false;
        }

        return (time() - $fileTime) < self::WEEK_IN_SECONDS;
    }

    private function shouldLoadCacheFile(?string $cacheFilePath): bool
    {
        if (! is_string($cacheFilePath)) {
            return false;
        }

        if (! file_exists($cacheFilePath)) {
            return false;
        }

        return $this->isFileYoungerThanWeek($cacheFilePath);
    }
}
