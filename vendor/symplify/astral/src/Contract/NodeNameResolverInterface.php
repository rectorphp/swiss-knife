<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\Astral\Contract;

use EasyCI20220115\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220115\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220115\PhpParser\Node $node) : ?string;
}
