<?php

// @see https://github.com/shipmonk-rnd/composer-dependency-analyser/
declare (strict_types=1);
namespace SwissKnife202605;

use SwissKnife202605\ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use SwissKnife202605\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
return (new Configuration())->ignoreErrorsOnExtension('ext-filter', [ErrorType::SHADOW_DEPENDENCY]);
