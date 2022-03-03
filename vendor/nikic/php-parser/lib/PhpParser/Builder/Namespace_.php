<?php

declare (strict_types=1);
namespace EasyCI20220303\PhpParser\Builder;

use EasyCI20220303\PhpParser;
use EasyCI20220303\PhpParser\BuilderHelpers;
use EasyCI20220303\PhpParser\Node;
use EasyCI20220303\PhpParser\Node\Stmt;
class Namespace_ extends \EasyCI20220303\PhpParser\Builder\Declaration
{
    private $name;
    private $stmts = [];
    /**
     * Creates a namespace builder.
     *
     * @param Node\Name|string|null $name Name of the namespace
     */
    public function __construct($name)
    {
        $this->name = null !== $name ? \EasyCI20220303\PhpParser\BuilderHelpers::normalizeName($name) : null;
    }
    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $this->stmts[] = \EasyCI20220303\PhpParser\BuilderHelpers::normalizeStmt($stmt);
        return $this;
    }
    /**
     * Returns the built node.
     *
     * @return Stmt\Namespace_ The built node
     */
    public function getNode() : \EasyCI20220303\PhpParser\Node
    {
        return new \EasyCI20220303\PhpParser\Node\Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
    }
}
