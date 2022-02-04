<?php

declare (strict_types=1);
namespace EasyCI20220204\PhpParser\ErrorHandler;

use EasyCI20220204\PhpParser\Error;
use EasyCI20220204\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements \EasyCI20220204\PhpParser\ErrorHandler
{
    public function handleError(\EasyCI20220204\PhpParser\Error $error)
    {
        throw $error;
    }
}
