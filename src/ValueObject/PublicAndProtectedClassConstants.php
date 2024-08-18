<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

final class PublicAndProtectedClassConstants
{
    /**
     * @var ClassConstMatch[]
     * @readonly
     */
    private $publicClassConstMatches;
    /**
     * @var ClassConstMatch[]
     * @readonly
     */
    private $protectedClassConstMatches;
    /**
     * @param ClassConstMatch[] $publicClassConstMatches
     * @param ClassConstMatch[] $protectedClassConstMatches
     */
    public function __construct(array $publicClassConstMatches, array $protectedClassConstMatches)
    {
        $this->publicClassConstMatches = $publicClassConstMatches;
        $this->protectedClassConstMatches = $protectedClassConstMatches;
    }
    /**
     * @return ClassConstMatch[]
     */
    public function getPublicClassConstMatches() : array
    {
        return $this->publicClassConstMatches;
    }
    /**
     * @return ClassConstMatch[]
     */
    public function getProtectedClassConstMatches() : array
    {
        return $this->protectedClassConstMatches;
    }
    public function getProtectedCount() : int
    {
        return \count($this->protectedClassConstMatches);
    }
    public function getPublicCount() : int
    {
        return \count($this->publicClassConstMatches);
    }
    public function isEmpty() : bool
    {
        return $this->publicClassConstMatches === [] && $this->protectedClassConstMatches === [];
    }
}
