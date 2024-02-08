<?php

namespace EasyCI202402\Illuminate\Contracts\Container;

use Exception;
use EasyCI202402\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
