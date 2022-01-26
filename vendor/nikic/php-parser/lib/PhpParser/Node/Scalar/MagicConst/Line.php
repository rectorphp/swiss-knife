<?php

declare (strict_types=1);
namespace EasyCI20220126\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220126\PhpParser\Node\Scalar\MagicConst;
class Line extends \EasyCI20220126\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__LINE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Line';
    }
}
