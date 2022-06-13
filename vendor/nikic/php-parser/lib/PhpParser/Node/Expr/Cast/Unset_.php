<?php

declare (strict_types=1);
namespace EasyCI202206\PhpParser\Node\Expr\Cast;

use EasyCI202206\PhpParser\Node\Expr\Cast;
class Unset_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}
