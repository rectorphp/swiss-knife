<?php

declare (strict_types=1);
namespace EasyCI20220131\PhpParser\Node\Expr;

use EasyCI20220131\PhpParser\Node\Expr;
abstract class AssignOp extends \EasyCI20220131\PhpParser\Node\Expr
{
    /** @var Expr Variable */
    public $var;
    /** @var Expr Expression */
    public $expr;
    /**
     * Constructs a compound assignment operation node.
     *
     * @param Expr  $var        Variable
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(\EasyCI20220131\PhpParser\Node\Expr $var, \EasyCI20220131\PhpParser\Node\Expr $expr, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->expr = $expr;
    }
    public function getSubNodeNames() : array
    {
        return ['var', 'expr'];
    }
}
