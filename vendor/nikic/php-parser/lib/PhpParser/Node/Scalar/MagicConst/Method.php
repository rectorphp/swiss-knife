<?php

declare (strict_types=1);
namespace SwissKnife202403\PhpParser\Node\Scalar\MagicConst;

use SwissKnife202403\PhpParser\Node\Scalar\MagicConst;
class Method extends MagicConst
{
    public function getName() : string
    {
        return '__METHOD__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Method';
    }
}
