<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

// Aliases for the whitelisted classes. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
if (!class_exists('ComposerAutoloaderInit66e11c8ec32e3c6b5e4e4f98e7388608', false) && !interface_exists('ComposerAutoloaderInit66e11c8ec32e3c6b5e4e4f98e7388608', false) && !trait_exists('ComposerAutoloaderInit66e11c8ec32e3c6b5e4e4f98e7388608', false)) {
    spl_autoload_call('EasyCI20220530\ComposerAutoloaderInit66e11c8ec32e3c6b5e4e4f98e7388608');
}
if (!class_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !interface_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !trait_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false)) {
    spl_autoload_call('EasyCI20220530\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator');
}
if (!class_exists('Normalizer', false) && !interface_exists('Normalizer', false) && !trait_exists('Normalizer', false)) {
    spl_autoload_call('EasyCI20220530\Normalizer');
}
if (!class_exists('ReturnTypeWillChange', false) && !interface_exists('ReturnTypeWillChange', false) && !trait_exists('ReturnTypeWillChange', false)) {
    spl_autoload_call('EasyCI20220530\ReturnTypeWillChange');
}

// Functions whitelisting. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
if (!function_exists('composerRequire66e11c8ec32e3c6b5e4e4f98e7388608')) {
    function composerRequire66e11c8ec32e3c6b5e4e4f98e7388608() {
        return \EasyCI20220530\composerRequire66e11c8ec32e3c6b5e4e4f98e7388608(...func_get_args());
    }
}
if (!function_exists('scanPath')) {
    function scanPath() {
        return \EasyCI20220530\scanPath(...func_get_args());
    }
}
if (!function_exists('lintFile')) {
    function lintFile() {
        return \EasyCI20220530\lintFile(...func_get_args());
    }
}
if (!function_exists('array_is_list')) {
    function array_is_list() {
        return \EasyCI20220530\array_is_list(...func_get_args());
    }
}
if (!function_exists('parseArgs')) {
    function parseArgs() {
        return \EasyCI20220530\parseArgs(...func_get_args());
    }
}
if (!function_exists('showHelp')) {
    function showHelp() {
        return \EasyCI20220530\showHelp(...func_get_args());
    }
}
if (!function_exists('formatErrorMessage')) {
    function formatErrorMessage() {
        return \EasyCI20220530\formatErrorMessage(...func_get_args());
    }
}
if (!function_exists('preprocessGrammar')) {
    function preprocessGrammar() {
        return \EasyCI20220530\preprocessGrammar(...func_get_args());
    }
}
if (!function_exists('resolveNodes')) {
    function resolveNodes() {
        return \EasyCI20220530\resolveNodes(...func_get_args());
    }
}
if (!function_exists('resolveMacros')) {
    function resolveMacros() {
        return \EasyCI20220530\resolveMacros(...func_get_args());
    }
}
if (!function_exists('resolveStackAccess')) {
    function resolveStackAccess() {
        return \EasyCI20220530\resolveStackAccess(...func_get_args());
    }
}
if (!function_exists('magicSplit')) {
    function magicSplit() {
        return \EasyCI20220530\magicSplit(...func_get_args());
    }
}
if (!function_exists('assertArgs')) {
    function assertArgs() {
        return \EasyCI20220530\assertArgs(...func_get_args());
    }
}
if (!function_exists('removeTrailingWhitespace')) {
    function removeTrailingWhitespace() {
        return \EasyCI20220530\removeTrailingWhitespace(...func_get_args());
    }
}
if (!function_exists('regex')) {
    function regex() {
        return \EasyCI20220530\regex(...func_get_args());
    }
}
if (!function_exists('execCmd')) {
    function execCmd() {
        return \EasyCI20220530\execCmd(...func_get_args());
    }
}
if (!function_exists('ensureDirExists')) {
    function ensureDirExists() {
        return \EasyCI20220530\ensureDirExists(...func_get_args());
    }
}
if (!function_exists('setproctitle')) {
    function setproctitle() {
        return \EasyCI20220530\setproctitle(...func_get_args());
    }
}
if (!function_exists('enum_exists')) {
    function enum_exists() {
        return \EasyCI20220530\enum_exists(...func_get_args());
    }
}

return $loader;
