<?php

namespace SwissKnife202405\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202405\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
