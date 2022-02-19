<?php

declare (strict_types=1);
namespace EasyCI20220219\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220219\PhpParser\Node\Scalar\MagicConst;
class Dir extends \EasyCI20220219\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__DIR__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Dir';
    }
}
