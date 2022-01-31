<?php

declare (strict_types=1);
namespace EasyCI20220131\PhpParser;

interface Builder
{
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : \EasyCI20220131\PhpParser\Node;
}
