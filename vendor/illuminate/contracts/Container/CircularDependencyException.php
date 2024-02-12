<?php

namespace SwissKnife202402\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202402\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
