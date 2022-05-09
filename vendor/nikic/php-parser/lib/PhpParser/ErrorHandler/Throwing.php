<?php

declare (strict_types=1);
namespace EasyCI20220509\PhpParser\ErrorHandler;

use EasyCI20220509\PhpParser\Error;
use EasyCI20220509\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements \EasyCI20220509\PhpParser\ErrorHandler
{
    public function handleError(\EasyCI20220509\PhpParser\Error $error)
    {
        throw $error;
    }
}
