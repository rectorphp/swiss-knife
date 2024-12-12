<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

final class VisibilityChangeStats
{
    private int $privateCount = 0;
    public function countPrivate() : void
    {
        ++$this->privateCount;
    }
    public function getPrivateCount() : int
    {
        return $this->privateCount;
    }
    public function merge(self $currentVisibilityChangeStats) : void
    {
        $this->privateCount += $currentVisibilityChangeStats->getPrivateCount();
    }
    public function hasAnyChange() : bool
    {
        return $this->privateCount > 0;
    }
}
