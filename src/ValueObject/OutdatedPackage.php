<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

final class OutdatedPackage
{
    /**
     * @readonly
     * @var string
     */
    public $name;
    /**
     * @readonly
     * @var string
     */
    public $latestVersion;
    /**
     * @readonly
     * @var string
     */
    public $installedVersion;
    /**
     * @readonly
     * @var string
     */
    public $installedAge;
    public function __construct(string $name, string $latestVersion, string $installedVersion, string $installedAge)
    {
        $this->name = $name;
        $this->latestVersion = $latestVersion;
        $this->installedVersion = $installedVersion;
        $this->installedAge = $installedAge;
    }
}
