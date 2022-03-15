<?php

declare (strict_types=1);
namespace EasyCI20220315\PhpParser\Builder;

use EasyCI20220315\PhpParser;
use EasyCI20220315\PhpParser\BuilderHelpers;
use EasyCI20220315\PhpParser\Node;
use EasyCI20220315\PhpParser\Node\Identifier;
use EasyCI20220315\PhpParser\Node\Stmt;
class EnumCase implements \EasyCI20220315\PhpParser\Builder
{
    protected $name;
    protected $value = null;
    protected $attributes = [];
    /** @var Node\AttributeGroup[] */
    protected $attributeGroups = [];
    /**
     * Creates an enum case builder.
     *
     * @param string|Identifier $name  Name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * Sets the value.
     *
     * @param Node\Expr|string|int $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = \EasyCI20220315\PhpParser\BuilderHelpers::normalizeValue($value);
        return $this;
    }
    /**
     * Sets doc comment for the constant.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment)
    {
        $this->attributes = ['comments' => [\EasyCI20220315\PhpParser\BuilderHelpers::normalizeDocComment($docComment)]];
        return $this;
    }
    /**
     * Adds an attribute group.
     *
     * @param Node\Attribute|Node\AttributeGroup $attribute
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addAttribute($attribute)
    {
        $this->attributeGroups[] = \EasyCI20220315\PhpParser\BuilderHelpers::normalizeAttribute($attribute);
        return $this;
    }
    /**
     * Returns the built enum case node.
     *
     * @return Stmt\EnumCase The built constant node
     */
    public function getNode() : \EasyCI20220315\PhpParser\Node
    {
        return new \EasyCI20220315\PhpParser\Node\Stmt\EnumCase($this->name, $this->value, $this->attributes, $this->attributeGroups);
    }
}
