<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\RobotLoader;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\RobotLoader\PhpClassLoader;
use Rector\SwissKnife\Tests\RobotLoader\Fixture\SingleClass;

final class PhpClassLoaderTest extends TestCase
{
    // intentionally not PSR-4 autoloadable, both live in the same file
    private const FIRST_THING = 'Rector\SwissKnife\Tests\RobotLoader\Fixture\FirstThing';

    private const SECOND_THING = 'Rector\SwissKnife\Tests\RobotLoader\Fixture\SecondThing';

    public function test(): void
    {
        $phpClassLoader = new PhpClassLoader();
        $classesToFilePaths = $phpClassLoader->load([__DIR__ . '/Fixture'], []);

        $this->assertArrayHasKey(SingleClass::class, $classesToFilePaths);
        $this->assertArrayHasKey(self::FIRST_THING, $classesToFilePaths);
        $this->assertArrayHasKey(self::SECOND_THING, $classesToFilePaths);

        $this->assertSame($classesToFilePaths[self::FIRST_THING], $classesToFilePaths[self::SECOND_THING]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $phpClassLoader = new PhpClassLoader();
        $classesToFilePaths = $phpClassLoader->load(
            [__DIR__ . '/Fixture'],
            [__DIR__ . '/Fixture/two_classes.php']
        );

        $this->assertArrayHasKey(SingleClass::class, $classesToFilePaths);
        $this->assertArrayNotHasKey(self::FIRST_THING, $classesToFilePaths);
    }
}
