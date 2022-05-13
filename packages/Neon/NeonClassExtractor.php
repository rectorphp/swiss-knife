<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Neon;

use EasyCI20220513\Nette\Neon\Decoder;
use EasyCI20220513\Nette\Neon\Node;
use EasyCI20220513\Nette\Neon\Node\ArrayItemNode;
use EasyCI20220513\Nette\Neon\Node\ArrayNode;
use EasyCI20220513\Nette\Neon\Node\LiteralNode;
use EasyCI20220513\Nette\Neon\Traverser;
use EasyCI20220513\Symplify\SmartFileSystem\SmartFileInfo;
final class NeonClassExtractor
{
    /**
     * @return string[]
     */
    public function extract(\EasyCI20220513\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : array
    {
        $neonDecoder = new \EasyCI20220513\Nette\Neon\Decoder();
        $node = $neonDecoder->parseToNode($fileInfo->getContents());
        $classKeyClassNames = $this->findClassNames($node);
        $stringStaticCallReferences = $this->findsStringStaticCallReferences($node);
        $servicesKeyList = $this->findsServicesKeyList($node);
        return \array_merge($classKeyClassNames, $stringStaticCallReferences, $servicesKeyList);
    }
    private function hasKeyValue(\EasyCI20220513\Nette\Neon\Node\ArrayItemNode $arrayItemNode, string $value) : bool
    {
        if (!$arrayItemNode->key instanceof \EasyCI20220513\Nette\Neon\Node\LiteralNode) {
            return \false;
        }
        return $arrayItemNode->key->toString() === $value;
    }
    /**
     * Finds "class: <name>"
     *
     * @return string[]
     */
    private function findClassNames(\EasyCI20220513\Nette\Neon\Node $node) : array
    {
        $classNames = [];
        $traverser = new \EasyCI20220513\Nette\Neon\Traverser();
        $traverser->traverse($node, function (\EasyCI20220513\Nette\Neon\Node $node) use(&$classNames) : ?Node {
            if (!$node instanceof \EasyCI20220513\Nette\Neon\Node\ArrayItemNode) {
                return $node;
            }
            if (!$this->hasKeyValue($node, 'class')) {
                return null;
            }
            if ($node->value instanceof \EasyCI20220513\Nette\Neon\Node\LiteralNode) {
                $classNames[] = $node->value->toString();
            }
            return null;
        });
        return $classNames;
    }
    /**
     * Finds <someStatic>::call
     *
     * @return string[]
     */
    private function findsStringStaticCallReferences(\EasyCI20220513\Nette\Neon\Node $node) : array
    {
        $classNames = [];
        $traverser = new \EasyCI20220513\Nette\Neon\Traverser();
        $traverser->traverse($node, function (\EasyCI20220513\Nette\Neon\Node $node) use(&$classNames) {
            if (!$node instanceof \EasyCI20220513\Nette\Neon\Node\LiteralNode) {
                return null;
            }
            $stringValue = $node->toString();
            if (\substr_count($stringValue, '::') !== 1) {
                return null;
            }
            // service name reference → skip
            if (\strncmp($stringValue, '@', \strlen('@')) === 0) {
                return null;
            }
            [$class, $method] = \explode('::', $stringValue);
            if (!\is_string($class)) {
                return null;
            }
            if ($class === '') {
                return null;
            }
            $classNames[] = $class;
            return null;
        });
        return $classNames;
    }
    /**
     * Finds "services: - <className>"
     *
     * @return string[]
     */
    private function findsServicesKeyList(\EasyCI20220513\Nette\Neon\Node $node) : array
    {
        $classNames = [];
        $traverser = new \EasyCI20220513\Nette\Neon\Traverser();
        $traverser->traverse($node, function (\EasyCI20220513\Nette\Neon\Node $node) use(&$classNames) {
            if (!$node instanceof \EasyCI20220513\Nette\Neon\Node\ArrayItemNode) {
                return null;
            }
            if (!$this->hasKeyValue($node, 'services')) {
                return null;
            }
            if (!$node->value instanceof \EasyCI20220513\Nette\Neon\Node\ArrayNode) {
                return null;
            }
            foreach ($node->value->items as $arrayItemNode) {
                if ($arrayItemNode->key !== null) {
                    continue;
                }
                if (!$arrayItemNode->value instanceof \EasyCI20220513\Nette\Neon\Node\LiteralNode) {
                    continue;
                }
                $classNames[] = $arrayItemNode->value->toString();
            }
            return null;
        });
        return $classNames;
    }
}
