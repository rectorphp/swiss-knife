<?php

declare (strict_types=1);
namespace EasyCI20220605\PhpParser\Builder;

use EasyCI20220605\PhpParser;
use EasyCI20220605\PhpParser\BuilderHelpers;
use EasyCI20220605\PhpParser\Node;
use EasyCI20220605\PhpParser\Node\Identifier;
use EasyCI20220605\PhpParser\Node\Name;
use EasyCI20220605\PhpParser\Node\Stmt;
class Enum_ extends \EasyCI20220605\PhpParser\Builder\Declaration
{
    protected $name;
    protected $scalarType = null;
    protected $implements = [];
    protected $uses = [];
    protected $enumCases = [];
    protected $constants = [];
    protected $methods = [];
    /** @var Node\AttributeGroup[] */
    protected $attributeGroups = [];
    /**
     * Creates an enum builder.
     *
     * @param string $name Name of the enum
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
     * Sets the scalar type.
     *
     * @param string|Identifier $type
     *
     * @return $this
     */
    public function setScalarType($scalarType)
    {
        $this->scalarType = \EasyCI20220605\PhpParser\BuilderHelpers::normalizeType($scalarType);
        return $this;
    }
    /**
     * Implements one or more interfaces.
     *
     * @param Name|string ...$interfaces Names of interfaces to implement
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function implement(...$interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->implements[] = \EasyCI20220605\PhpParser\BuilderHelpers::normalizeName($interface);
        }
        return $this;
    }
    /**
     * Adds a statement.
     *
     * @param Stmt|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $stmt = \EasyCI20220605\PhpParser\BuilderHelpers::normalizeNode($stmt);
        $targets = [\EasyCI20220605\PhpParser\Node\Stmt\TraitUse::class => &$this->uses, \EasyCI20220605\PhpParser\Node\Stmt\EnumCase::class => &$this->enumCases, \EasyCI20220605\PhpParser\Node\Stmt\ClassConst::class => &$this->constants, \EasyCI20220605\PhpParser\Node\Stmt\ClassMethod::class => &$this->methods];
        $class = \get_class($stmt);
        if (!isset($targets[$class])) {
            throw new \LogicException(\sprintf('Unexpected node of type "%s"', $stmt->getType()));
        }
        $targets[$class][] = $stmt;
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
        $this->attributeGroups[] = \EasyCI20220605\PhpParser\BuilderHelpers::normalizeAttribute($attribute);
        return $this;
    }
    /**
     * Returns the built class node.
     *
     * @return Stmt\Enum_ The built enum node
     */
    public function getNode() : \EasyCI20220605\PhpParser\Node
    {
        return new \EasyCI20220605\PhpParser\Node\Stmt\Enum_($this->name, ['scalarType' => $this->scalarType, 'implements' => $this->implements, 'stmts' => \array_merge($this->uses, $this->enumCases, $this->constants, $this->methods), 'attrGroups' => $this->attributeGroups], $this->attributes);
    }
}
