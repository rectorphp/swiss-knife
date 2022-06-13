<?php

declare (strict_types=1);
namespace EasyCI202206\Symplify\Astral\Contract;

use EasyCI202206\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(Node $node) : bool;
    public function resolve(Node $node) : ?string;
}
