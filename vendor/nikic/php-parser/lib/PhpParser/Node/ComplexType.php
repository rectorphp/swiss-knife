<?php

declare (strict_types=1);
namespace SwissKnife202402\PhpParser\Node;

use SwissKnife202402\PhpParser\NodeAbstract;
/**
 * This is a base class for complex types, including nullable types and union types.
 *
 * It does not provide any shared behavior and exists only for type-checking purposes.
 */
abstract class ComplexType extends NodeAbstract
{
}
