<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220115\Symfony\Component\Console\Helper;

use EasyCI20220115\Symfony\Component\Console\Output\ConsoleOutputInterface;
use EasyCI20220115\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220115\Symfony\Component\Process\Exception\ProcessFailedException;
use EasyCI20220115\Symfony\Component\Process\Process;
/**
 * The ProcessHelper class provides helpers to run external processes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ProcessHelper extends \EasyCI20220115\Symfony\Component\Console\Helper\Helper
{
    /**
     * Runs an external process.
     *
     * @param array|Process $cmd      An instance of Process or an array of the command and arguments
     * @param callable|null $callback A PHP callback to run whenever there is some
     *                                output available on STDOUT or STDERR
     */
    public function run(\EasyCI20220115\Symfony\Component\Console\Output\OutputInterface $output, $cmd, string $error = null, callable $callback = null, int $verbosity = \EasyCI20220115\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERY_VERBOSE) : \EasyCI20220115\Symfony\Component\Process\Process
    {
        if (!\class_exists(\EasyCI20220115\Symfony\Component\Process\Process::class)) {
            throw new \LogicException('The ProcessHelper cannot be run as the Process component is not installed. Try running "compose require symfony/process".');
        }
        if ($output instanceof \EasyCI20220115\Symfony\Component\Console\Output\ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }
        $formatter = $this->getHelperSet()->get('debug_formatter');
        if ($cmd instanceof \EasyCI20220115\Symfony\Component\Process\Process) {
            $cmd = [$cmd];
        }
        if (\is_string($cmd[0] ?? null)) {
            $process = new \EasyCI20220115\Symfony\Component\Process\Process($cmd);
            $cmd = [];
        } elseif (($cmd[0] ?? null) instanceof \EasyCI20220115\Symfony\Component\Process\Process) {
            $process = $cmd[0];
            unset($cmd[0]);
        } else {
            throw new \InvalidArgumentException(\sprintf('Invalid command provided to "%s()": the command should be an array whose first element is either the path to the binary to run or a "Process" object.', __METHOD__));
        }
        if ($verbosity <= $output->getVerbosity()) {
            $output->write($formatter->start(\spl_object_hash($process), $this->escapeString($process->getCommandLine())));
        }
        if ($output->isDebug()) {
            $callback = $this->wrapCallback($output, $process, $callback);
        }
        $process->run($callback, $cmd);
        if ($verbosity <= $output->getVerbosity()) {
            $message = $process->isSuccessful() ? 'Command ran successfully' : \sprintf('%s Command did not run successfully', $process->getExitCode());
            $output->write($formatter->stop(\spl_object_hash($process), $message, $process->isSuccessful()));
        }
        if (!$process->isSuccessful() && null !== $error) {
            $output->writeln(\sprintf('<error>%s</error>', $this->escapeString($error)));
        }
        return $process;
    }
    /**
     * Runs the process.
     *
     * This is identical to run() except that an exception is thrown if the process
     * exits with a non-zero exit code.
     *
     * @param array|Process $cmd      An instance of Process or a command to run
     * @param callable|null $callback A PHP callback to run whenever there is some
     *                                output available on STDOUT or STDERR
     *
     * @throws ProcessFailedException
     *
     * @see run()
     */
    public function mustRun(\EasyCI20220115\Symfony\Component\Console\Output\OutputInterface $output, $cmd, string $error = null, callable $callback = null) : \EasyCI20220115\Symfony\Component\Process\Process
    {
        $process = $this->run($output, $cmd, $error, $callback);
        if (!$process->isSuccessful()) {
            throw new \EasyCI20220115\Symfony\Component\Process\Exception\ProcessFailedException($process);
        }
        return $process;
    }
    /**
     * Wraps a Process callback to add debugging output.
     */
    public function wrapCallback(\EasyCI20220115\Symfony\Component\Console\Output\OutputInterface $output, \EasyCI20220115\Symfony\Component\Process\Process $process, callable $callback = null) : callable
    {
        if ($output instanceof \EasyCI20220115\Symfony\Component\Console\Output\ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }
        $formatter = $this->getHelperSet()->get('debug_formatter');
        return function ($type, $buffer) use($output, $process, $callback, $formatter) {
            $output->write($formatter->progress(\spl_object_hash($process), $this->escapeString($buffer), \EasyCI20220115\Symfony\Component\Process\Process::ERR === $type));
            if (null !== $callback) {
                $callback($type, $buffer);
            }
        };
    }
    private function escapeString(string $str) : string
    {
        return \str_replace('<', '\\<', $str);
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'process';
    }
}
