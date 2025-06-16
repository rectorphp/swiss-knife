<?php

namespace SwissKnife202506\Illuminate\Contracts\Container;

use Exception;
use SwissKnife202506\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
