<?php

declare (strict_types=1);
namespace SwissKnife202504\PhpParser\Node;

use SwissKnife202504\PhpParser\Node;
use SwissKnife202504\PhpParser\NodeAbstract;
class Attribute extends NodeAbstract
{
    /** @var Name Attribute name */
    public $name;
    /** @var list<Arg> Attribute arguments */
    public $args;
    /**
     * @param Node\Name $name Attribute name
     * @param list<Arg> $args Attribute arguments
     * @param array<string, mixed> $attributes Additional node attributes
     */
    public function __construct(Name $name, array $args = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->name = $name;
        $this->args = $args;
    }
    public function getSubNodeNames() : array
    {
        return ['name', 'args'];
    }
    public function getType() : string
    {
        return 'Attribute';
    }
}
