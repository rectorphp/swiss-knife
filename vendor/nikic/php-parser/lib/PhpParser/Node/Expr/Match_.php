<?php

declare (strict_types=1);
namespace EasyCI20220225\PhpParser\Node\Expr;

use EasyCI20220225\PhpParser\Node;
use EasyCI20220225\PhpParser\Node\MatchArm;
class Match_ extends \EasyCI20220225\PhpParser\Node\Expr
{
    /** @var Node\Expr */
    public $cond;
    /** @var MatchArm[] */
    public $arms;
    /**
     * @param MatchArm[] $arms
     */
    public function __construct(\EasyCI20220225\PhpParser\Node\Expr $cond, array $arms = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->arms = $arms;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'arms'];
    }
    public function getType() : string
    {
        return 'Expr_Match';
    }
}
