<?php

declare (strict_types=1);
namespace SwissKnife202412\PhpParser\Node\Stmt;

use SwissKnife202412\PhpParser\Node;
class Switch_ extends Node\Stmt
{
    /** @var Node\Expr Condition */
    public Node\Expr $cond;
    /** @var Case_[] Case list */
    public array $cases;
    /**
     * Constructs a case node.
     *
     * @param Node\Expr $cond Condition
     * @param Case_[] $cases Case list
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $cases, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->cases = $cases;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'cases'];
    }
    public function getType() : string
    {
        return 'Stmt_Switch';
    }
}
