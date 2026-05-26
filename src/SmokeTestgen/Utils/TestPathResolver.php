<?php

declare(strict_types=1);

namespace Rector\SwissKnife\SmokeTestgen\Utils;

use Rector\SwissKnife\SmokeTestgen\Contract\TestByPackageSubscriberInterface;

final class TestPathResolver
{
    public static function resolve(
        TestByPackageSubscriberInterface $testByPackageSubscriber,
        string $smokeTestsDirectory
    ): string {
        $absolutePath = $testByPackageSubscriber->getTemplateFilePath();
        $testFileBasename = pathinfo($absolutePath, PATHINFO_BASENAME);

        return $smokeTestsDirectory . '/' . $testFileBasename;
    }
}
