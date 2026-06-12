<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Finder;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Finder\FilesFinder;

final class FilesFinderExtendedTest extends TestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-files-finder-' . uniqid();
        FileSystem::createDir($this->tempDirectory . '/twig');
        FileSystem::write($this->tempDirectory . '/twig/template.twig', 'content', null);
        FileSystem::write($this->tempDirectory . '/single.json', '{"a":1}', null);
        FileSystem::write($this->tempDirectory . '/fixture.yml', 'key: value', null);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    public function testFindTwigFiles(): void
    {
        $files = FilesFinder::findTwigFiles([$this->tempDirectory]);

        $this->assertCount(1, $files);
    }

    public function testFindJsonFilesWithSingleFile(): void
    {
        $jsonFile = realpath($this->tempDirectory . '/single.json');
        $this->assertNotFalse($jsonFile);

        $files = FilesFinder::findJsonFiles([$jsonFile]);

        $this->assertCount(1, $files);
    }

    public function testFindYamlFiles(): void
    {
        $files = FilesFinder::findYamlFiles([$this->tempDirectory]);

        $this->assertCount(1, $files);
    }
}
