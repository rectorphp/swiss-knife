<?php

declare (strict_types=1);
namespace EasyCI20220313\Symplify\Astral\Contract;

use EasyCI20220313\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220313\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220313\PhpParser\Node $node) : ?string;
}
