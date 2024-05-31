<?php

namespace Rector\SwissKnife\Tests\Resolver\StaticClassConstResolver\Fixture;

final class FileWithStaticConstCalls
{
    public const ITEM_NAME = '...';

    public const ITEM_PRICE = 100;

    public function run()
    {
        $hash = static::ITEM_NAME . '_' . static::ITEM_PRICE;
    }
}
