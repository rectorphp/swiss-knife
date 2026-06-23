<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\CachedPhpParser;

use Nette\Utils\FileSystem;
use Override;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\Tests\AbstractTestCase;
use RuntimeException;

final class CachedPhpParserTest extends AbstractTestCase
{
    private CachedPhpParser $cachedPhpParser;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->cachedPhpParser = $this->make(CachedPhpParser::class);
    }

    public function testParseFileCachesResult(): void
    {
        $filePath = __DIR__ . '/../PhpParser/Finder/ClassConstFinder/Fixture/SomeClassWithConstants.php';

        $firstParse = $this->cachedPhpParser->parseFile($filePath);
        $secondParse = $this->cachedPhpParser->parseFile($filePath);

        $this->assertSame($firstParse, $secondParse);
        $this->assertNotEmpty($firstParse);
    }

    public function testParseError(): void
    {
        $tempFile = sys_get_temp_dir() . '/swiss-knife-parse-error-' . uniqid() . '.php';
        FileSystem::write($tempFile, '<?php invalid syntax here', null);

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Could not parse file "' . $tempFile . '"');

            $this->cachedPhpParser->parseFile($tempFile);
        } finally {
            FileSystem::delete($tempFile);
        }
    }
}
