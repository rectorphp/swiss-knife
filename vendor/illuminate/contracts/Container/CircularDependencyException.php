<?php

namespace SwissKnife202504\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202504\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
