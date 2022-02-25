<?php

declare (strict_types=1);
namespace EasyCI20220225\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220225\PhpParser\Node\Scalar\MagicConst;
class Namespace_ extends \EasyCI20220225\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__NAMESPACE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Namespace';
    }
}
