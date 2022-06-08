<?php

declare (strict_types=1);
namespace EasyCI20220608\Symplify\Astral\Contract;

use EasyCI20220608\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(Node $node) : bool;
    public function resolve(Node $node) : ?string;
}
