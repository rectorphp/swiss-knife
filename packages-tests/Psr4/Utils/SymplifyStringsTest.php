<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Psr4\Utils;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Psr4\Utils\SymplifyStrings;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SymplifyStringsTest extends AbstractKernelTestCase
{
    private SymplifyStrings $symplifyStrings;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->symplifyStrings = $this->getService(SymplifyStrings::class);
    }

    /**
     * @param string[] $values
     */
    #[DataProvider('provideData')]
    public function test(array $values, string $expectedSharedSuffix): void
    {
        $sharedSuffix = $this->symplifyStrings->findSharedSlashedSuffix($values);
        $this->assertSame($expectedSharedSuffix, $sharedSuffix);
    }

    public static function provideData(): Iterator
    {
        yield [['Car', 'BusCar'], 'Car'];
        yield [['Apple\Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Apple/Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Components\ChatFriends', 'ChatFriends\ChatFriends'], 'ChatFriends'];
    }
}
