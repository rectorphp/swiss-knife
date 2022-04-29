<?php

declare (strict_types=1);
namespace EasyCI20220429\PhpParser\Builder;

use EasyCI20220429\PhpParser;
use EasyCI20220429\PhpParser\BuilderHelpers;
use EasyCI20220429\PhpParser\Node;
use EasyCI20220429\PhpParser\Node\Identifier;
use EasyCI20220429\PhpParser\Node\Name;
use EasyCI20220429\PhpParser\Node\Stmt;
use EasyCI20220429\PhpParser\Node\ComplexType;
class Property implements \EasyCI20220429\PhpParser\Builder
{
    protected $name;
    protected $flags = 0;
    protected $default = null;
    protected $attributes = [];
    /** @var null|Identifier|Name|NullableType */
    protected $type;
    /** @var Node\AttributeGroup[] */
    protected $attributeGroups = [];
    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
     * Makes the property public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic()
    {
        $this->flags = \EasyCI20220429\PhpParser\BuilderHelpers::addModifier($this->flags, \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC);
        return $this;
    }
    /**
     * Makes the property protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected()
    {
        $this->flags = \EasyCI20220429\PhpParser\BuilderHelpers::addModifier($this->flags, \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED);
        return $this;
    }
    /**
     * Makes the property private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate()
    {
        $this->flags = \EasyCI20220429\PhpParser\BuilderHelpers::addModifier($this->flags, \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE);
        return $this;
    }
    /**
     * Makes the property static.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeStatic()
    {
        $this->flags = \EasyCI20220429\PhpParser\BuilderHelpers::addModifier($this->flags, \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_STATIC);
        return $this;
    }
    /**
     * Makes the property readonly.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeReadonly()
    {
        $this->flags = \EasyCI20220429\PhpParser\BuilderHelpers::addModifier($this->flags, \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_READONLY);
        return $this;
    }
    /**
     * Sets default value for the property.
     *
     * @param mixed $value Default value to use
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDefault($value)
    {
        $this->default = \EasyCI20220429\PhpParser\BuilderHelpers::normalizeValue($value);
        return $this;
    }
    /**
     * Sets doc comment for the property.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment)
    {
        $this->attributes = ['comments' => [\EasyCI20220429\PhpParser\BuilderHelpers::normalizeDocComment($docComment)]];
        return $this;
    }
    /**
     * Sets the property type for PHP 7.4+.
     *
     * @param string|Name|Identifier|ComplexType $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = \EasyCI20220429\PhpParser\BuilderHelpers::normalizeType($type);
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
        $this->attributeGroups[] = \EasyCI20220429\PhpParser\BuilderHelpers::normalizeAttribute($attribute);
        return $this;
    }
    /**
     * Returns the built class node.
     *
     * @return Stmt\Property The built property node
     */
    public function getNode() : \EasyCI20220429\PhpParser\Node
    {
        return new \EasyCI20220429\PhpParser\Node\Stmt\Property($this->flags !== 0 ? $this->flags : \EasyCI20220429\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC, [new \EasyCI20220429\PhpParser\Node\Stmt\PropertyProperty($this->name, $this->default)], $this->attributes, $this->type, $this->attributeGroups);
    }
}
