<?php

declare(strict_types=1);

namespace MagedIn\Lab\Model;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Process\Process as ComponentProcess;

class Process
{
    /**
     * @param array $command
     * @param callable|null $callback
     * @param bool $tty
     * @param bool $pty
     * @param array $env
     * @param string|null $cwd
     * @param null $input
     * @param float|null $timeout
     * @return ComponentProcess
     */
    public static function run(
        array $command,
        callable $callback = null,
        bool $tty = false,
        bool $pty = false,
        array $env = [],
        string $cwd = null,
        $input = null,
        ?float $timeout = 60
    ): ComponentProcess {
        /** @var ProcessFactory $processFactory */
        $processFactory = ObjectManager::getInstance()->create(ProcessFactory::class);
        $process = $processFactory->create($command, $cwd, $env, $input, $timeout);
        $process->setTty($tty);
        $process->setPty($pty);
        $process->run($callback, $env);
        return $process;
    }
}
