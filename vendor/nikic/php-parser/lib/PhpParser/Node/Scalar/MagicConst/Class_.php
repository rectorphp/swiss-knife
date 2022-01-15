<?php

declare (strict_types=1);
namespace EasyCI20220115\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220115\PhpParser\Node\Scalar\MagicConst;
class Class_ extends \EasyCI20220115\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__CLASS__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Class';
    }
}
