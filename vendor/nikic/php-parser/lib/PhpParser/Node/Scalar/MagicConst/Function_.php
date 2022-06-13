<?php

declare (strict_types=1);
namespace EasyCI202206\PhpParser\Node\Scalar\MagicConst;

use EasyCI202206\PhpParser\Node\Scalar\MagicConst;
class Function_ extends MagicConst
{
    public function getName() : string
    {
        return '__FUNCTION__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Function';
    }
}
