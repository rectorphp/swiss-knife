<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220116\Symfony\Component\Console\Output;

/**
 * ConsoleOutputInterface is the interface implemented by ConsoleOutput class.
 * This adds information about stderr and section output stream.
 *
 * @author Dariusz Górecki <darek.krk@gmail.com>
 */
interface ConsoleOutputInterface extends \EasyCI20220116\Symfony\Component\Console\Output\OutputInterface
{
    /**
     * Gets the OutputInterface for errors.
     */
    public function getErrorOutput() : \EasyCI20220116\Symfony\Component\Console\Output\OutputInterface;
    public function setErrorOutput(\EasyCI20220116\Symfony\Component\Console\Output\OutputInterface $error);
    public function section() : \EasyCI20220116\Symfony\Component\Console\Output\ConsoleSectionOutput;
}
