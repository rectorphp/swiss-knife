<?php

declare (strict_types=1);
namespace EasyCI20220223\PhpParser\Node\Expr\Cast;

use EasyCI20220223\PhpParser\Node\Expr\Cast;
class Object_ extends \EasyCI20220223\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
