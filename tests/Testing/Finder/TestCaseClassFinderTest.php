<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\Finder;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\Finder\TestCaseClassFinder;

final class TestCaseClassFinderTest extends TestCase
{
    public function testFindInDirectories(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-test-case-finder-' . uniqid();
        FileSystem::createDir($tempDirectory);

        FileSystem::write($tempDirectory . '/SomeUnitTest.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempTests;

use PHPUnit\Framework\TestCase;

final class SomeUnitTest extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(true);
    }
}
PHP, null);

        $testCaseClassFinder = new TestCaseClassFinder();
        $classes = $testCaseClassFinder->findInDirectories([$tempDirectory]);

        $this->assertArrayHasKey('TempTests\SomeUnitTest', $classes);

        FileSystem::delete($tempDirectory);
    }
}
