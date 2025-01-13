<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace SwissKnife202501\Nette\Loaders;

use SwissKnife202501\Nette;
use SwissKnife202501\Nette\Utils\FileSystem;
use SplFileInfo;
/**
 * Nette auto loader is responsible for loading classes and interfaces.
 *
 * <code>
 * $loader = new Nette\Loaders\RobotLoader;
 * $loader->addDirectory('app');
 * $loader->excludeDirectory('app/exclude');
 * $loader->setTempDirectory('temp');
 * $loader->register();
 * </code>
 */
class RobotLoader
{
    private const RetryLimit = 3;
    /** @var string[] */
    public $ignoreDirs = ['.*', '*.old', '*.bak', '*.tmp', 'temp'];
    /** @var string[] */
    public $acceptFiles = ['*.php'];
    /**
     * @var bool
     */
    private $autoRebuild = \true;
    /**
     * @var bool
     */
    private $reportParseErrors = \true;
    /** @var string[] */
    private $scanPaths = [];
    /** @var string[] */
    private $excludeDirs = [];
    /** @var array<string, array{string, int}>  class => [file, time] */
    private $classes = [];
    /**
     * @var bool
     */
    private $cacheLoaded = \false;
    /**
     * @var bool
     */
    private $refreshed = \false;
    /** @var array<string, int>  class => counter */
    private $missingClasses = [];
    /** @var array<string, int>  file => mtime */
    private $emptyFiles = [];
    /**
     * @var string|null
     */
    private $tempDirectory;
    /**
     * @var bool
     */
    private $needSave = \false;
    public function __construct()
    {
        if (!\extension_loaded('tokenizer')) {
            throw new Nette\NotSupportedException('PHP extension Tokenizer is not loaded.');
        }
    }
    public function __destruct()
    {
        if ($this->needSave) {
            $this->saveCache();
        }
    }
    /**
     * Register autoloader.
     * @return static
     */
    public function register(bool $prepend = \false)
    {
        \spl_autoload_register([$this, 'tryLoad'], \true, $prepend);
        return $this;
    }
    /**
     * Handles autoloading of classes, interfaces or traits.
     */
    public function tryLoad(string $type) : void
    {
        $this->loadCache();
        $missing = $this->missingClasses[$type] ?? null;
        if ($missing >= self::RetryLimit) {
            return;
        }
        [$file, $mtime] = $this->classes[$type] ?? null;
        if ($this->autoRebuild) {
            if (!$this->refreshed) {
                if (!$file || !\is_file($file)) {
                    $this->refreshClasses();
                    [$file] = $this->classes[$type] ?? null;
                    $this->needSave = \true;
                } elseif (\filemtime($file) !== $mtime) {
                    $this->updateFile($file);
                    [$file] = $this->classes[$type] ?? null;
                    $this->needSave = \true;
                }
            }
            if (!$file || !\is_file($file)) {
                $this->missingClasses[$type] = ++$missing;
                $this->needSave = $this->needSave || $file || $missing <= self::RetryLimit;
                unset($this->classes[$type]);
                $file = null;
            }
        }
        if ($file) {
            (static function ($file) {
                require $file;
            })($file);
        }
    }
    /**
     * Add path or paths to list.
     * @return static
     */
    public function addDirectory(string ...$paths)
    {
        $this->scanPaths = \array_merge($this->scanPaths, $paths);
        return $this;
    }
    /**
     * @return static
     */
    public function reportParseErrors(bool $state = \true)
    {
        $this->reportParseErrors = $state;
        return $this;
    }
    /**
     * Excludes path or paths from list.
     * @return static
     */
    public function excludeDirectory(string ...$paths)
    {
        $this->excludeDirs = \array_merge($this->excludeDirs, $paths);
        return $this;
    }
    /**
     * @return array<string, string>  class => filename
     */
    public function getIndexedClasses() : array
    {
        $this->loadCache();
        $res = [];
        foreach ($this->classes as $class => [$file]) {
            $res[$class] = $file;
        }
        return $res;
    }
    /**
     * Rebuilds class list cache.
     */
    public function rebuild() : void
    {
        $this->cacheLoaded = \true;
        $this->classes = $this->missingClasses = $this->emptyFiles = [];
        $this->refreshClasses();
        if ($this->tempDirectory) {
            $this->saveCache();
        }
    }
    /**
     * Refreshes class list cache.
     */
    public function refresh() : void
    {
        $this->loadCache();
        if (!$this->refreshed) {
            $this->refreshClasses();
            $this->saveCache();
        }
    }
    /**
     * Refreshes $this->classes & $this->emptyFiles.
     */
    private function refreshClasses() : void
    {
        $this->refreshed = \true;
        // prevents calling refreshClasses() or updateFile() in tryLoad()
        $files = $this->emptyFiles;
        $classes = [];
        foreach ($this->classes as $class => [$file, $mtime]) {
            $files[$file] = $mtime;
            $classes[$file][] = $class;
        }
        $this->classes = $this->emptyFiles = [];
        foreach ($this->scanPaths as $path) {
            $iterator = \is_file($path) ? [new SplFileInfo($path)] : $this->createFileIterator($path);
            foreach ($iterator as $fileInfo) {
                $mtime = $fileInfo->getMTime();
                $file = $fileInfo->getPathname();
                $foundClasses = isset($files[$file]) && $files[$file] === $mtime ? $classes[$file] ?? [] : $this->scanPhp($file);
                if (!$foundClasses) {
                    $this->emptyFiles[$file] = $mtime;
                }
                $files[$file] = $mtime;
                $classes[$file] = [];
                // prevents the error when adding the same file twice
                foreach ($foundClasses as $class) {
                    if (isset($this->classes[$class])) {
                        throw new Nette\InvalidStateException(\sprintf('Ambiguous class %s resolution; defined in %s and in %s.', $class, $this->classes[$class][0], $file));
                    }
                    $this->classes[$class] = [$file, $mtime];
                    unset($this->missingClasses[$class]);
                }
            }
        }
    }
    /**
     * Creates an iterator scanning directory for PHP files and subdirectories.
     * @throws Nette\IOException if path is not found
     */
    private function createFileIterator(string $dir) : Nette\Utils\Finder
    {
        if (!\is_dir($dir)) {
            throw new Nette\IOException(\sprintf("Directory '%s' not found.", $dir));
        }
        $dir = \realpath($dir) ?: $dir;
        // realpath does not work in phar
        $disallow = [];
        foreach (\array_merge($this->ignoreDirs, $this->excludeDirs) as $item) {
            if ($item = \realpath($item)) {
                $disallow[$item] = \true;
            }
        }
        return Nette\Utils\Finder::findFiles($this->acceptFiles)->filter($filter = function (SplFileInfo $file) use($disallow) {
            return $file->getRealPath() === \false || !isset($disallow[$file->getRealPath()]);
        })->descentFilter($filter)->from($dir)->exclude($this->ignoreDirs);
    }
    private function updateFile(string $file) : void
    {
        foreach ($this->classes as $class => [$prevFile]) {
            if ($file === $prevFile) {
                unset($this->classes[$class]);
            }
        }
        $foundClasses = \is_file($file) ? $this->scanPhp($file) : [];
        foreach ($foundClasses as $class) {
            [$prevFile, $prevMtime] = $this->classes[$class] ?? null;
            if (isset($prevFile) && @\filemtime($prevFile) !== $prevMtime) {
                // @ file may not exist
                $this->updateFile($prevFile);
                [$prevFile] = $this->classes[$class] ?? null;
            }
            if (isset($prevFile)) {
                throw new Nette\InvalidStateException(\sprintf('Ambiguous class %s resolution; defined in %s and in %s.', $class, $prevFile, $file));
            }
            $this->classes[$class] = [$file, \filemtime($file)];
        }
    }
    /**
     * Searches classes, interfaces and traits in PHP file.
     * @return string[]
     */
    private function scanPhp(string $file) : array
    {
        $code = \file_get_contents($file);
        $expected = \false;
        $namespace = $name = '';
        $level = $minLevel = 0;
        $classes = [];
        try {
            $tokens = \token_get_all($code, \TOKEN_PARSE);
        } catch (\ParseError $e) {
            if ($this->reportParseErrors) {
                $rp = new \ReflectionProperty($e, 'file');
                $rp->setAccessible(\true);
                $rp->setValue($e, $file);
                throw $e;
            }
            $tokens = [];
        }
        foreach ($tokens as $token) {
            switch (\is_array($token) ? $token[0] : $token) {
                case \T_COMMENT:
                case \T_DOC_COMMENT:
                case \T_WHITESPACE:
                    continue 2;
                case \T_STRING:
                case \T_NAME_QUALIFIED:
                    if ($expected) {
                        $name .= \is_array($token) ? $token[1] : $token;
                    }
                    continue 2;
                case \T_NAMESPACE:
                case \T_CLASS:
                case \T_INTERFACE:
                case \T_TRAIT:
                case \PHP_VERSION_ID < 80100 ? \T_CLASS : \T_ENUM:
                    $expected = \is_array($token) ? $token[0] : $token;
                    $name = '';
                    continue 2;
            }
            if ($expected) {
                if ($expected === \T_NAMESPACE) {
                    $namespace = $name ? $name . '\\' : '';
                    $minLevel = (\is_array($token) ? $token[1] : $token) === '{' ? 1 : 0;
                } elseif ($name && $level === $minLevel) {
                    $classes[] = $namespace . $name;
                }
                $expected = null;
            }
            if ((\is_array($token) ? $token[1] : $token) === '{') {
                $level++;
            } elseif ((\is_array($token) ? $token[1] : $token) === '}') {
                $level--;
            }
        }
        return $classes;
    }
    /********************* caching ****************d*g**/
    /**
     * Sets auto-refresh mode.
     * @return static
     */
    public function setAutoRefresh(bool $state = \true)
    {
        $this->autoRebuild = $state;
        return $this;
    }
    /**
     * Sets path to temporary directory.
     * @return static
     */
    public function setTempDirectory(string $dir)
    {
        if (!FileSystem::isAbsolute($dir)) {
            throw new Nette\InvalidArgumentException("Temporary directory must be absolute, '{$dir}' given.");
        }
        FileSystem::createDir($dir);
        $this->tempDirectory = $dir;
        return $this;
    }
    /**
     * Loads class list from cache.
     */
    private function loadCache() : void
    {
        if ($this->cacheLoaded) {
            return;
        }
        $this->cacheLoaded = \true;
        $file = $this->generateCacheFileName();
        // Solving atomicity to work everywhere is really pain in the ass.
        // 1) We want to do as little as possible IO calls on production and also directory and file can be not writable (#19)
        // so on Linux we include the file directly without shared lock, therefore, the file must be created atomically by renaming.
        // 2) On Windows file cannot be renamed-to while is open (ie by include() #11), so we have to acquire a lock.
        $lock = \defined('PHP_WINDOWS_VERSION_BUILD') ? $this->acquireLock("{$file}.lock", \LOCK_SH) : null;
        $data = @(include $file);
        // @ file may not exist
        if (\is_array($data)) {
            [$this->classes, $this->missingClasses, $this->emptyFiles] = $data;
            return;
        }
        if ($lock) {
            \flock($lock, \LOCK_UN);
            // release shared lock so we can get exclusive
        }
        $lock = $this->acquireLock("{$file}.lock", \LOCK_EX);
        // while waiting for exclusive lock, someone might have already created the cache
        $data = @(include $file);
        // @ file may not exist
        if (\is_array($data)) {
            [$this->classes, $this->missingClasses, $this->emptyFiles] = $data;
            return;
        }
        $this->classes = $this->missingClasses = $this->emptyFiles = [];
        $this->refreshClasses();
        $this->saveCache($lock);
        // On Windows concurrent creation and deletion of a file can cause a 'permission denied' error,
        // therefore, we will not delete the lock file. Windows is really annoying.
    }
    /**
     * Writes class list to cache.
     * @param  resource  $lock
     */
    private function saveCache($lock = null) : void
    {
        // we have to acquire a lock to be able safely rename file
        // on Linux: that another thread does not rename the same named file earlier
        // on Windows: that the file is not read by another thread
        $file = $this->generateCacheFileName();
        $lock = $lock ?: $this->acquireLock("{$file}.lock", \LOCK_EX);
        $code = "<?php\nreturn " . \var_export([$this->classes, $this->missingClasses, $this->emptyFiles], \true) . ";\n";
        if (\file_put_contents("{$file}.tmp", $code) !== \strlen($code) || !\rename("{$file}.tmp", $file)) {
            @\unlink("{$file}.tmp");
            // @ file may not exist
            throw new \RuntimeException(\sprintf("Unable to create '%s'.", $file));
        }
        if (\function_exists('opcache_invalidate')) {
            @\opcache_invalidate($file, \true);
            // @ can be restricted
        }
    }
    /** @return resource */
    private function acquireLock(string $file, int $mode)
    {
        $handle = @\fopen($file, 'w');
        // @ is escalated to exception
        if (!$handle) {
            throw new \RuntimeException(\sprintf("Unable to create file '%s'. %s", $file, \error_get_last()['message']));
        } elseif (!@\flock($handle, $mode)) {
            // @ is escalated to exception
            throw new \RuntimeException(\sprintf("Unable to acquire %s lock on file '%s'. %s", $mode & \LOCK_EX ? 'exclusive' : 'shared', $file, \error_get_last()['message']));
        }
        return $handle;
    }
    private function generateCacheFileName() : string
    {
        if (!$this->tempDirectory) {
            throw new \LogicException('Set path to temporary directory using setTempDirectory().');
        }
        return $this->tempDirectory . '/' . \hash(\PHP_VERSION_ID < 80100 ? 'md5' : 'xxh128', \serialize($this->generateCacheKey())) . '.php';
    }
    protected function generateCacheKey() : array
    {
        return [$this->ignoreDirs, $this->acceptFiles, $this->scanPaths, $this->excludeDirs, 'v2'];
    }
}
