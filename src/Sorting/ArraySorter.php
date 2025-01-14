<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Sorting;

use Webmozart\Assert\Assert;

final class ArraySorter
{
    /**
     * @param array<mixed[]> $items
     * @return array<mixed[]>
     */
    public static function putSharedFirst(array $items): array
    {
        Assert::allIsArray($items);

        usort($items, function (array $firstItem, array $secondItem): int {
            $firstHasBoth = count(array_filter($firstItem, fn ($value) => $value !== null)) === count($firstItem);

            $secondHasBoth = count(array_filter($secondItem, fn ($value) => $value !== null)) === count($secondItem);

            return $secondHasBoth <=> $firstHasBoth;
        });

        return $items;
    }


}
