<?php

namespace Rector\SwissKnife\Behastan\ValueObject;

use Rector\SwissKnife\Behastan\Contract\MaskInterface;
abstract class AbstractMask implements MaskInterface
{
    /**
     * @readonly
     * @var string
     */
    public $mask;
    /**
     * @readonly
     * @var string
     */
    public $filePath;
    public function __construct(string $mask, string $filePath)
    {
        $this->mask = $mask;
        $this->filePath = $filePath;
    }
}
