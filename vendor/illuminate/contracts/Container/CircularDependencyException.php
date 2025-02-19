<?php

namespace SwissKnife202502\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202502\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
