<?php

namespace SwissKnife202412\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202412\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
