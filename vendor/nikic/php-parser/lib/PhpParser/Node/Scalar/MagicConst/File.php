<?php

declare (strict_types=1);
namespace EasyCI202211\PhpParser\Node\Scalar\MagicConst;

use EasyCI202211\PhpParser\Node\Scalar\MagicConst;
class File extends MagicConst
{
    public function getName() : string
    {
        return '__FILE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_File';
    }
}
