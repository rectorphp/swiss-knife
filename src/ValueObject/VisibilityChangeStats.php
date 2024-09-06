<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

final class VisibilityChangeStats
{
    /**
     * @var int
     */
    private $privateCount = 0;
    /**
     * @var int
     */
    private $publicCount = 0;
    public function countPrivate() : void
    {
        ++$this->privateCount;
    }
    public function countPublic() : void
    {
        ++$this->publicCount;
    }
    public function getPrivateCount() : int
    {
        return $this->privateCount;
    }
    public function getPublicCount() : int
    {
        return $this->publicCount;
    }
    public function merge(self $currentVisibilityChangeStats) : void
    {
        $this->publicCount += $currentVisibilityChangeStats->getPublicCount();
        $this->privateCount += $currentVisibilityChangeStats->getPrivateCount();
    }
    public function hasAnyChange() : bool
    {
        return $this->privateCount > 0 || $this->publicCount > 0;
    }
}
