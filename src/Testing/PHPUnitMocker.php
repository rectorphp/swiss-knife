<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Helped class to create PHPUnit mock class
 */
final class PHPUnitMocker extends TestCase
{
    /**
     * @param class-string $classObject
     */
    public function create(string $classObject): MockObject
    {
        return $this->createMock($classObject);
    }
}
