<?php

// scoper-autoload.php @generated by PhpScoper

$loader = (static function () {
    // Backup the autoloaded Composer files
    $existingComposerAutoloadFiles = $GLOBALS['__composer_autoload_files'] ?? [];

    $loader = require_once __DIR__.'/autoload.php';
    // Ensure InstalledVersions is available
    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

    // Restore the backup and ensure the excluded files are properly marked as loaded
    $GLOBALS['__composer_autoload_files'] = \array_merge(
        $existingComposerAutoloadFiles,
        \array_fill_keys(['6e3fae29631ef280660b3cdad06f25a8', '4dc28194476b018fdb0a0339c6423bce'], true)
    );

    return $loader;
})();

// Class aliases. For more information see:
// https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#class-aliases
if (!function_exists('humbug_phpscoper_expose_class')) {
    function humbug_phpscoper_expose_class($exposed, $prefixed) {
        if (!class_exists($exposed, false) && !interface_exists($exposed, false) && !trait_exists($exposed, false)) {
            spl_autoload_call($prefixed);
        }
    }
}
humbug_phpscoper_expose_class('ComposerAutoloaderInit74b09cee93121d03df4c70af1af76412', 'SwissKnife202412\ComposerAutoloaderInit74b09cee93121d03df4c70af1af76412');

// Function aliases. For more information see:
// https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
if (!function_exists('formatErrorMessage')) { function formatErrorMessage() { return \SwissKnife202412\formatErrorMessage(...func_get_args()); } }
if (!function_exists('grapheme_extract')) { function grapheme_extract() { return \SwissKnife202412\grapheme_extract(...func_get_args()); } }
if (!function_exists('grapheme_stripos')) { function grapheme_stripos() { return \SwissKnife202412\grapheme_stripos(...func_get_args()); } }
if (!function_exists('grapheme_stristr')) { function grapheme_stristr() { return \SwissKnife202412\grapheme_stristr(...func_get_args()); } }
if (!function_exists('grapheme_strlen')) { function grapheme_strlen() { return \SwissKnife202412\grapheme_strlen(...func_get_args()); } }
if (!function_exists('grapheme_strpos')) { function grapheme_strpos() { return \SwissKnife202412\grapheme_strpos(...func_get_args()); } }
if (!function_exists('grapheme_strripos')) { function grapheme_strripos() { return \SwissKnife202412\grapheme_strripos(...func_get_args()); } }
if (!function_exists('grapheme_strrpos')) { function grapheme_strrpos() { return \SwissKnife202412\grapheme_strrpos(...func_get_args()); } }
if (!function_exists('grapheme_strstr')) { function grapheme_strstr() { return \SwissKnife202412\grapheme_strstr(...func_get_args()); } }
if (!function_exists('grapheme_substr')) { function grapheme_substr() { return \SwissKnife202412\grapheme_substr(...func_get_args()); } }
if (!function_exists('parseArgs')) { function parseArgs() { return \SwissKnife202412\parseArgs(...func_get_args()); } }
if (!function_exists('showHelp')) { function showHelp() { return \SwissKnife202412\showHelp(...func_get_args()); } }
if (!function_exists('trigger_deprecation')) { function trigger_deprecation() { return \SwissKnife202412\trigger_deprecation(...func_get_args()); } }

return $loader;
