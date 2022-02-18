<?php

declare (strict_types=1);
namespace EasyCI20220218\PhpParser\Node\Stmt;

use EasyCI20220218\PhpParser\Node;
abstract class TraitUseAdaptation extends \EasyCI20220218\PhpParser\Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
