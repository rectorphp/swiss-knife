<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSkip([
        // invalid syntax test fixture
        __DIR__ . '/tests/PhpParser/Finder/ClassConstantFetchFinder/Fixture/Error/ParseError.php',
        // intentionally odd-styled fixture, measured line-by-line
        __DIR__ . '/tests/Lines/Fixture/source.php',
    ])
    ->withPreparedSets(psr12: true, common: true)
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles();
