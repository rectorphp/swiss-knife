<?php

namespace SwissKnife202601\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202601\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
