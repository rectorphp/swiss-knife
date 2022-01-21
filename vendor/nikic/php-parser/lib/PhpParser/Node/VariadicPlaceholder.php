<?php

declare (strict_types=1);
namespace EasyCI20220121\PhpParser\Node;

use EasyCI20220121\PhpParser\NodeAbstract;
/**
 * Represents the "..." in "foo(...)" of the first-class callable syntax.
 */
class VariadicPlaceholder extends \EasyCI20220121\PhpParser\NodeAbstract
{
    /**
     * Create a variadic argument placeholder (first-class callable syntax).
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
    public function getType() : string
    {
        return 'VariadicPlaceholder';
    }
    public function getSubNodeNames() : array
    {
        return [];
    }
}
