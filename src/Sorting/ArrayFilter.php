<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Sorting;

use Webmozart\Assert\Assert;

final class ArrayFilter
{
    /**
     * @param array<mixed[]> $items
     * @return array<mixed[]>
     */
    public static function filterOnlyAtLeast2(array $items): array
    {
        Assert::allIsArray($items);

        return array_filter($items, function (array $item): bool {
            $nonEmptyValues = array_filter($item);

            // +1 for the package name
            return count($nonEmptyValues) >= 3;
        });
    }
}
