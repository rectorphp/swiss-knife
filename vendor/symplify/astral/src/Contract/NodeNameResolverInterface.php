<?php

declare (strict_types=1);
namespace EasyCI20220316\Symplify\Astral\Contract;

use EasyCI20220316\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220316\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220316\PhpParser\Node $node) : ?string;
}
