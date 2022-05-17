<?php

declare (strict_types=1);
namespace EasyCI20220517\PhpParser\Node;

use EasyCI20220517\PhpParser\NodeAbstract;
/**
 * This is a base class for complex types, including nullable types and union types.
 *
 * It does not provide any shared behavior and exists only for type-checking purposes.
 */
abstract class ComplexType extends \EasyCI20220517\PhpParser\NodeAbstract
{
}
