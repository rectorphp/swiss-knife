<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SwissKnife202408\Symfony\Component\Process\Messenger;

use SwissKnife202408\Symfony\Component\Process\Exception\ProcessFailedException;
use SwissKnife202408\Symfony\Component\Process\Exception\RunProcessFailedException;
use SwissKnife202408\Symfony\Component\Process\Process;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RunProcessMessageHandler
{
    public function __invoke(RunProcessMessage $message) : RunProcessContext
    {
        $process = new Process($message->command, $message->cwd, $message->env, $message->input, $message->timeout);
        try {
            return new RunProcessContext($message, $process->mustRun());
        } catch (ProcessFailedException $e) {
            throw new RunProcessFailedException($e, new RunProcessContext($message, $e->getProcess()));
        }
    }
}
