<?php

declare (strict_types=1);
namespace SwissKnife202502\PhpParser\Node\Scalar\MagicConst;

use SwissKnife202502\PhpParser\Node\Scalar\MagicConst;
class Property extends MagicConst
{
    public function getName() : string
    {
        return '__PROPERTY__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Property';
    }
}
