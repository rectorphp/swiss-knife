<?php

declare (strict_types=1);
namespace EasyCI20220126\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220126\PhpParser\Node\Scalar\MagicConst;
class Function_ extends \EasyCI20220126\PhpParser\Node\Scalar\MagicConst
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
