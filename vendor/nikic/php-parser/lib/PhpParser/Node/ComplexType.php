<?php

declare (strict_types=1);
namespace EasyCI20220131\PhpParser\Node;

use EasyCI20220131\PhpParser\NodeAbstract;
/**
 * This is a base class for complex types, including nullable types and union types.
 *
 * It does not provide any shared behavior and exists only for type-checking purposes.
 */
abstract class ComplexType extends \EasyCI20220131\PhpParser\NodeAbstract
{
}
