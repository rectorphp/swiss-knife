<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\NodeVisitor;

use EasyCI20220516\Nette\Utils\Strings;
use EasyCI20220516\PhpParser\Comment\Doc;
use EasyCI20220516\PhpParser\Node;
use EasyCI20220516\PhpParser\Node\Stmt\ClassLike;
use EasyCI20220516\PhpParser\NodeTraverser;
use EasyCI20220516\PhpParser\NodeVisitorAbstract;
final class ClassNameNodeVisitor extends \EasyCI20220516\PhpParser\NodeVisitorAbstract
{
    /**
     * @var string
     * @see https://regex101.com/r/LXmPYG/1
     */
    private const API_TAG_REGEX = '#@api\\b#';
    /**
     * @var string|null
     */
    private $className = null;
    /**
     * @param Node\Stmt[] $nodes
     * @return Node\Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->className = null;
        return $nodes;
    }
    public function enterNode(\EasyCI20220516\PhpParser\Node $node) : ?int
    {
        if (!$node instanceof \EasyCI20220516\PhpParser\Node\Stmt\ClassLike) {
            return null;
        }
        if ($node->name === null) {
            return null;
        }
        if ($this->hasApiTag($node)) {
            return null;
        }
        $this->className = $node->namespacedName->toString();
        return \EasyCI20220516\PhpParser\NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
    }
    public function getClassName() : ?string
    {
        return $this->className;
    }
    private function hasApiTag(\EasyCI20220516\PhpParser\Node\Stmt\ClassLike $classLike) : bool
    {
        $doc = $classLike->getDocComment();
        if (!$doc instanceof \EasyCI20220516\PhpParser\Comment\Doc) {
            return \false;
        }
        $matches = \EasyCI20220516\Nette\Utils\Strings::match($doc->getText(), self::API_TAG_REGEX);
        return $matches !== null;
    }
}
