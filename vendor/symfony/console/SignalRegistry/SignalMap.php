<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SwissKnife202412\Symfony\Component\Console\SignalRegistry;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SignalMap
{
    private static array $map;
    public static function getSignalName(int $signal) : ?string
    {
        if (!\extension_loaded('pcntl')) {
            return null;
        }
        if (!isset(self::$map)) {
            $r = new \ReflectionExtension('pcntl');
            $c = $r->getConstants();
            $map = \array_filter($c, fn($k) => \strncmp($k, 'SIG', \strlen('SIG')) === 0 && \strncmp($k, 'SIG_', \strlen('SIG_')) !== 0, \ARRAY_FILTER_USE_KEY);
            self::$map = \array_flip($map);
        }
        return self::$map[$signal] ?? null;
    }
}
