<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\FeatureCounter\ValueObject;

use Rector\SwissKnife\Lines\FeatureCounter\Enum\PhpVersion;

final class PhpFeature
{
    /**
     * @var PhpVersion::*
     * @readonly
     */
    private string $phpVersion;

    /**
     * @readonly
     */
    private string $name;

    /**
     * @var callable
     */
    private $nodeTrigger;

    private int $count;

    /**
     * @param PhpVersion::* $phpVersion
     * @param callable $nodeTrigger
     */
    public function __construct(string $phpVersion, string $name, $nodeTrigger, int $count = 0)
    {
        $this->phpVersion = $phpVersion;
        $this->name = $name;
        $this->nodeTrigger = $nodeTrigger;
        $this->count = $count;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNodeTrigger(): callable
    {
        return $this->nodeTrigger;
    }

    public function increaseCount(): void
    {
        ++$this->count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
