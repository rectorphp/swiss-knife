<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Behastan\ValueObject;

final class MaskCollection
{
    /**
     * @param AbstractMask[] $masks
     */
    public function __construct(
        private readonly array $masks
    ) {
    }

    /**
     * @param class-string<AbstractMask> $type
     */
    public function countByType(string $type): int
    {
        $masksByType = $this->byType($type);
        return count($masksByType);
    }

    public function count(): int
    {
        return count($this->masks);
    }

    /**
     * @return AbstractMask[]
     */
    public function all(): array
    {
        return $this->masks;
    }

    /**
     * @template TMask as AbstractMask
     *
     * @param class-string<TMask> $type
     * @return TMask[]
     */
    public function byType(string $type): array
    {
        return array_filter($this->masks, fn (AbstractMask $mask): bool => $mask instanceof $type);
    }
}
