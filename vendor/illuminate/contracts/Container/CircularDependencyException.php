<?php

namespace SwissKnife202410\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202410\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
