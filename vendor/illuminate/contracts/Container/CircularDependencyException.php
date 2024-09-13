<?php

namespace SwissKnife202409\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202409\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
