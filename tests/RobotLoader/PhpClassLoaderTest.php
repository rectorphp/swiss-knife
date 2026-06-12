<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\RobotLoader;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\RobotLoader\PhpClassLoader;

final class PhpClassLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $phpClassLoader = new PhpClassLoader();

        $indexedClasses = $phpClassLoader->load(
            [__DIR__ . '/../Finder/MultiClassFixture'],
            []
        );

        $this->assertArrayHasKey(
            'Rector\SwissKnife\Tests\Finder\MultiClassFixture\FirstClassInFile',
            $indexedClasses
        );
        $this->assertArrayHasKey(
            'Rector\SwissKnife\Tests\Finder\MultiClassFixture\SecondClassInFile',
            $indexedClasses
        );
    }
}
