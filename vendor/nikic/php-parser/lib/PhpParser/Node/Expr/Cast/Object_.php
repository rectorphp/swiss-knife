<?php

declare (strict_types=1);
namespace EasyCI202301\PhpParser\Node\Expr\Cast;

use EasyCI202301\PhpParser\Node\Expr\Cast;
class Object_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
