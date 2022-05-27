<?php

declare (strict_types=1);
namespace EasyCI20220527\PhpParser\Node\Stmt;

use EasyCI20220527\PhpParser\Node;
abstract class TraitUseAdaptation extends \EasyCI20220527\PhpParser\Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
