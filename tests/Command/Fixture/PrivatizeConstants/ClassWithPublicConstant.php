<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command\Fixture\PrivatizeConstants;

final class ClassWithPublicConstant
{
    public const PUBLIC_CONSTANT = 'value';

    public function run(): string
    {
        return self::PUBLIC_CONSTANT;
    }
}
