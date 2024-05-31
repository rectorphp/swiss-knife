<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Helpers;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Helpers\ClassNameResolver;
use Rector\SwissKnife\Tests\Helpers\Fixture\SomeClass;

final class ClassNameResolverTest extends TestCase
{
    public function test(): void
    {
        $fileContents = FileSystem::read(__DIR__ . '/Fixture/SomeClass.php');
        $resolvedFullyQualifiedName = ClassNameResolver::resolveFromFileContents($fileContents);

        $this->assertSame(SomeClass::class, $resolvedFullyQualifiedName);
    }
}
