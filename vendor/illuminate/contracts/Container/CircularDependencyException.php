<?php

namespace SwissKnife202507\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202507\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
