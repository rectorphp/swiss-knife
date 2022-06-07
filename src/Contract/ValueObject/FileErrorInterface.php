<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Contract\ValueObject;

interface FileErrorInterface
{
    public function getErrorMessage() : string;
    public function getRelativeFilePath() : string;
}
