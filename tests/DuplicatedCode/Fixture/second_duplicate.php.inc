<?php

function firstDuplicate(): int
{
    $total = 0;
    $items = [1, 2, 3, 4, 5];

    foreach ($items as $item) {
        $total += $item * 2;
    }

    return $total;
}
