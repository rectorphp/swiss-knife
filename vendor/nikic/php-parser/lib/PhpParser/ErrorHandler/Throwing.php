<?php

declare (strict_types=1);
namespace SwissKnife202412\PhpParser\ErrorHandler;

use SwissKnife202412\PhpParser\Error;
use SwissKnife202412\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error) : void
    {
        throw $error;
    }
}
