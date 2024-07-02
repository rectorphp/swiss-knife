<?php

namespace SwissKnife202407\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202407\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
