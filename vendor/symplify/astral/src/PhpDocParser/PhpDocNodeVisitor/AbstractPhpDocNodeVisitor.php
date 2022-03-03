<?php

declare (strict_types=1);
namespace EasyCI20220303\Symplify\Astral\PhpDocParser\PhpDocNodeVisitor;

use EasyCI20220303\PHPStan\PhpDocParser\Ast\Node;
use EasyCI20220303\Symplify\Astral\PhpDocParser\Contract\PhpDocNodeVisitorInterface;
/**
 * Inspired by https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitorAbstract.php
 */
abstract class AbstractPhpDocNodeVisitor implements \EasyCI20220303\Symplify\Astral\PhpDocParser\Contract\PhpDocNodeVisitorInterface
{
    public function beforeTraverse(\EasyCI20220303\PHPStan\PhpDocParser\Ast\Node $node) : void
    {
    }
    /**
     * @return int|Node|null
     */
    public function enterNode(\EasyCI20220303\PHPStan\PhpDocParser\Ast\Node $node)
    {
        return null;
    }
    /**
     * @return null|int|\PhpParser\Node|Node[] Replacement node (or special return)
     */
    public function leaveNode(\EasyCI20220303\PHPStan\PhpDocParser\Ast\Node $node)
    {
        return null;
    }
    public function afterTraverse(\EasyCI20220303\PHPStan\PhpDocParser\Ast\Node $node) : void
    {
    }
}
