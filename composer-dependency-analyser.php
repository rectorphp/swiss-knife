<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPackage('phpunit/phpunit', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    // used by Rector\SwissKnife\Enum\SymfonyClass
    ->ignoreErrorsOnPackage('symfony/dependency-injection', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    // test fixtures
    ->ignoreErrorsOnPath(
        __DIR__ . '/tests/EntityClassResolver/Fixture/Anything/SomeAttributeDocument.php',
        [ErrorType::UNKNOWN_CLASS]
    )
    ->ignoreErrorsOnPath(
        __DIR__ . '/tests/PhpParser/NodeVisitor/MockedClassNameCollectingNodeVisitor/Fixture',
        [ErrorType::UNKNOWN_CLASS]
    );
