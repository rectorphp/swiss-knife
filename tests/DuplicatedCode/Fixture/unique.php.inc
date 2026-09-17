<?php

function uniqueLogic(string $name): string
{
    $greeting = 'Hello ' . $name;

    if ($name === '') {
        return 'Hello stranger';
    }

    return strtoupper($greeting);
}
