<?php

declare (strict_types=1);
namespace EasyCI20220524\PhpParser\ErrorHandler;

use EasyCI20220524\PhpParser\Error;
use EasyCI20220524\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements \EasyCI20220524\PhpParser\ErrorHandler
{
    public function handleError(\EasyCI20220524\PhpParser\Error $error)
    {
        throw $error;
    }
}
