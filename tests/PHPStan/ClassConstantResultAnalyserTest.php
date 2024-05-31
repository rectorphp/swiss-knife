<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PHPStan;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PHPStan\ClassConstantResultAnalyser;
use Rector\SwissKnife\ValueObject\ClassConstMatch;

final class ClassConstantResultAnalyserTest extends TestCase
{
    private ClassConstantResultAnalyser $classConstantResultAnalyser;

    protected function setUp(): void
    {
        $this->classConstantResultAnalyser = new ClassConstantResultAnalyser();
    }

    public function test(): void
    {
        $phpstanResult = [
            'files' => [
                'some_file_path.php' => [
                    'messages' => [
                        [
                            'line' => 10,
                            'message' => 'Access to undefined constant App\\SomeClass::SOME_CONSTANT',
                        ],
                    ],
                ],
            ],
        ];

        $publicAndProtectedClassConstants = $this->classConstantResultAnalyser->analyseResult($phpstanResult);

        $this->assertFalse($publicAndProtectedClassConstants->isEmpty());
        $this->assertSame(0, $publicAndProtectedClassConstants->getPublicCount());
        $this->assertSame(1, $publicAndProtectedClassConstants->getProtectedCount());

        $onlyProtectedClassConstMatch = $publicAndProtectedClassConstants->getProtectedClassConstMatches()[0];
        $this->assertInstanceOf(ClassConstMatch::class, $onlyProtectedClassConstMatch);

        // $this->assertSame('SOME_CONST', $onlyProtectedClassConstMatch->getConstantName());
        $this->assertSame('App\SomeClass', $onlyProtectedClassConstMatch->getClassName());
    }
}
