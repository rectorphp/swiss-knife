<?php

namespace SwissKnife202501\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202501\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
