<?php

declare (strict_types=1);
namespace EasyCI20220302\PhpParser\Node\Expr\Cast;

use EasyCI20220302\PhpParser\Node\Expr\Cast;
class Array_ extends \EasyCI20220302\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
