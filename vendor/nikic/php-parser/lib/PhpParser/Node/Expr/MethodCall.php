<?php

declare (strict_types=1);
namespace SwissKnife202508\PhpParser\Node\Expr;

use SwissKnife202508\PhpParser\Node;
use SwissKnife202508\PhpParser\Node\Arg;
use SwissKnife202508\PhpParser\Node\Expr;
use SwissKnife202508\PhpParser\Node\Identifier;
use SwissKnife202508\PhpParser\Node\VariadicPlaceholder;
class MethodCall extends CallLike
{
    /** @var Expr Variable holding object */
    public $var;
    /** @var Identifier|Expr Method name */
    public $name;
    /** @var array<Arg|VariadicPlaceholder> Arguments */
    public $args;
    /**
     * Constructs a function call node.
     *
     * @param Expr $var Variable holding object
     * @param string|Identifier|Expr $name Method name
     * @param array<Arg|VariadicPlaceholder> $args Arguments
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, $name, array $args = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
        $this->args = $args;
    }
    public function getSubNodeNames() : array
    {
        return ['var', 'name', 'args'];
    }
    public function getType() : string
    {
        return 'Expr_MethodCall';
    }
    public function getRawArgs() : array
    {
        return $this->args;
    }
}
