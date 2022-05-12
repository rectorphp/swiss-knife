<?php

declare (strict_types=1);
namespace EasyCI20220512\Symplify\Astral\PhpDocParser\PhpDocNodeVisitor;

use EasyCI20220512\PHPStan\PhpDocParser\Ast\Node;
use EasyCI20220512\Symplify\Astral\PhpDocParser\ValueObject\PhpDocAttributeKey;
/**
 * @api
 *
 * Mirrors
 * https://github.com/nikic/PHP-Parser/blob/d520bc9e1d6203c35a1ba20675b79a051c821a9e/lib/PhpParser/NodeVisitor/CloningVisitor.php
 */
final class CloningPhpDocNodeVisitor extends \EasyCI20220512\Symplify\Astral\PhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor
{
    public function enterNode(\EasyCI20220512\PHPStan\PhpDocParser\Ast\Node $node) : \EasyCI20220512\PHPStan\PhpDocParser\Ast\Node
    {
        $clonedNode = clone $node;
        $clonedNode->setAttribute(\EasyCI20220512\Symplify\Astral\PhpDocParser\ValueObject\PhpDocAttributeKey::ORIG_NODE, $node);
        return $clonedNode;
    }
}
