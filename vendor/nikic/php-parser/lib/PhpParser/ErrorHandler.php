<?php

declare (strict_types=1);
namespace EasyCI20220530\PhpParser;

interface ErrorHandler
{
    /**
     * Handle an error generated during lexing, parsing or some other operation.
     *
     * @param Error $error The error that needs to be handled
     */
    public function handleError(\EasyCI20220530\PhpParser\Error $error);
}
