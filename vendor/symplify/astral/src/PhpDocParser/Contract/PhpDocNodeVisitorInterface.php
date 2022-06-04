<?php

declare (strict_types=1);
namespace EasyCI20220604\Symplify\Astral\PhpDocParser\Contract;

use EasyCI20220604\PHPStan\PhpDocParser\Ast\Node;
/**
 * Inspired by https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php
 */
interface PhpDocNodeVisitorInterface
{
    public function beforeTraverse(\EasyCI20220604\PHPStan\PhpDocParser\Ast\Node $node) : void;
    /**
     * @return int|\PHPStan\PhpDocParser\Ast\Node|null
     */
    public function enterNode(\EasyCI20220604\PHPStan\PhpDocParser\Ast\Node $node);
    /**
     * @return int|\PhpParser\Node|mixed[]|null Replacement node (or special return)
     */
    public function leaveNode(\EasyCI20220604\PHPStan\PhpDocParser\Ast\Node $node);
    public function afterTraverse(\EasyCI20220604\PHPStan\PhpDocParser\Ast\Node $node) : void;
}
