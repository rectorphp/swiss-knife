<?php

declare (strict_types=1);
namespace EasyCI202209\PhpParser\Node\Stmt;

use EasyCI202209\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames() : array
    {
        return [];
    }
    public function getType() : string
    {
        return 'Stmt_Nop';
    }
}
