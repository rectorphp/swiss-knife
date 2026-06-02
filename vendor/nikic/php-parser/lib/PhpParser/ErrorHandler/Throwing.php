<?php

declare (strict_types=1);
namespace SwissKnife202606\PhpParser\ErrorHandler;

use SwissKnife202606\PhpParser\Error;
use SwissKnife202606\PhpParser\ErrorHandler;
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
