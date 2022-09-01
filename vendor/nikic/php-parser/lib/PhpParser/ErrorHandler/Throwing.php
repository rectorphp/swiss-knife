<?php

declare (strict_types=1);
namespace EasyCI202209\PhpParser\ErrorHandler;

use EasyCI202209\PhpParser\Error;
use EasyCI202209\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
