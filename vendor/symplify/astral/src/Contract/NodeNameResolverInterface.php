<?php

declare (strict_types=1);
namespace EasyCI20220429\Symplify\Astral\Contract;

use EasyCI20220429\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220429\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220429\PhpParser\Node $node) : ?string;
}
