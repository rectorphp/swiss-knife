<?php

declare (strict_types=1);
namespace EasyCI20220605\PhpParser\Node\Expr\Cast;

use EasyCI20220605\PhpParser\Node\Expr\Cast;
class Unset_ extends \EasyCI20220605\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}
