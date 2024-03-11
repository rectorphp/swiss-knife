<?php

namespace SwissKnife202403\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202403\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
