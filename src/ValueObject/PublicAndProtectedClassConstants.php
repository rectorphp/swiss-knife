<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

final readonly class PublicAndProtectedClassConstants
{
    /**
     * @param ClassConstMatch[] $publicClassConstMatch
     * @param ClassConstMatch[] $protectedClassConstMatch
     */
    public function __construct(
        private array $publicClassConstMatch,
        private array $protectedClassConstMatch
    ) {
    }

    /**
     * @return ClassConstMatch[]
     */
    public function getPublicClassConstMatch(): array
    {
        return $this->publicClassConstMatch;
    }

    /**
     * @return ClassConstMatch[]
     */
    public function getProtectedClassConstMatch(): array
    {
        return $this->protectedClassConstMatch;
    }

    public function getProtectedCount(): int
    {
        return count($this->protectedClassConstMatch);
    }

    public function getPublicCount(): int
    {
        return count($this->publicClassConstMatch);
    }

    public function isEmpty(): bool
    {
        return $this->publicClassConstMatch === [] && $this->protectedClassConstMatch === [];
    }
}
