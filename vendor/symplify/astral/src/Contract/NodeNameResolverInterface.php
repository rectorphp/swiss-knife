<?php

declare (strict_types=1);
namespace EasyCI20220131\Symplify\Astral\Contract;

use EasyCI20220131\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220131\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220131\PhpParser\Node $node) : ?string;
}
