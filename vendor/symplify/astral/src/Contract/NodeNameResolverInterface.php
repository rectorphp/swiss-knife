<?php

declare (strict_types=1);
namespace EasyCI20220609\Symplify\Astral\Contract;

use EasyCI20220609\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(Node $node) : bool;
    public function resolve(Node $node) : ?string;
}
