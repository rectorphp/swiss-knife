<?php

namespace SwissKnife202408\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202408\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
