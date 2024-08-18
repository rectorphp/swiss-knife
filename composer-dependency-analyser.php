<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->addPathToScan(__DIR__ . '/src', false)
    ->addPathToScan(__DIR__ . '/tests', false)
    // test fixture
    ->ignoreErrorsOnPath(
        __DIR__ . '/tests/EntityClassResolver/Fixture/Anything/SomeAttributeDocument.php',
        [ErrorType::UNKNOWN_CLASS]
    );
