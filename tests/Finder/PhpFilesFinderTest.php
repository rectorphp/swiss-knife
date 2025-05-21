<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Finder;

use Rector\SwissKnife\Tests\AbstractTestCase;

final class PhpFilesFinderTest extends AbstractTestCase
{
    public function testExcludeByFnMatch(): void
    {
        $files = \Rector\SwissKnife\Finder\PhpFilesFinder::find([__DIR__ . '/Fixture'], ['*Controller.php']);

        $this->assertSame(1, count($files));

        sort($files); // make order predictable across operating systems
        $file = array_pop($files);
        $this->assertNotNull($file);
        $this->assertStringContainsString('AModel.php', $file->getRealPath());
    }

    public function testExcludeByFnMatch2(): void
    {
        $files = \Rector\SwissKnife\Finder\PhpFilesFinder::find([__DIR__ . '/Fixture'], ['*Model.php']);

        $this->assertSame(2, count($files));

        sort($files); // make order predictable across operating systems
        $file = array_pop($files);
        $this->assertNotNull($file);
        $this->assertStringContainsString('BController.php', $file->getRealPath());

        $file = array_pop($files);
        $this->assertNotNull($file);
        $this->assertStringContainsString('AController.php', $file->getRealPath());
    }
}
