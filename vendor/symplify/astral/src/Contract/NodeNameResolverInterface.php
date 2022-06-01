<?php

declare (strict_types=1);
namespace EasyCI20220601\Symplify\Astral\Contract;

use EasyCI20220601\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220601\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220601\PhpParser\Node $node) : ?string;
}
