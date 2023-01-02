<?php

declare (strict_types=1);
namespace EasyCI202301\PhpParser\ErrorHandler;

use EasyCI202301\PhpParser\Error;
use EasyCI202301\PhpParser\ErrorHandler;
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
