<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// @see https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md
use Nette\Utils\Strings;

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'EasyCI' . $timestamp,
    'expose-constants' => ['#^SYMFONY\_[\p{L}_]+$#'],
    'exclude-namespaces' => ['#^Symplify\\\\EasyCI#', '#^Symfony\\\\Polyfill#'],
    'exclude-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'stubs/PHPUnit/PHPUnit_Framework_TestCase.php',
    ],
    'patchers' => [
        // unprefix test case class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/Testing/UnitTestFilter.php')) {
                return $content;
            }

            $content = Strings::replace(
                $content,
                '#' . $prefix . '\\\\PHPUnit\\\\Framework\\\\TestCase#',
                'PHPUnit\Framework\TestCase'
            );

            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\PHPUnit_Framework_TestCase#',
                'PHPUnit_Framework_TestCase'
            );
        },

        // unprefix kernerl test case class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/Testing/UnitTestFilter.php')) {
                return $content;
            }

            $content = Strings::replace(
                $content,
                '#' . $prefix . '\\\\Symfony\\\\Bundle\\\\FrameworkBundle\\\\Test\\\\KernelTestCase#',
                'Symfony\Bundle\FrameworkBundle\Test\KernelTestCase'
            );

            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\Symfony\\\\Component\\\\Form\\\\Test\\\\TypeTestCase',
                'Symfony\Component\Form\Test\TypeTestCase'
            );
        },
    ],
];
