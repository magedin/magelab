<?php

declare(strict_types=1);

namespace MageLab\Model;

use DI\DependencyException;
use DI\NotFoundException;
use MageLab\ObjectManager;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @param array $command
     * @param string|null $cwd
     * @param array|null $env
     * @param $input
     * @param float|null $timeout
     * @return Process
     */
    public function create(
        array $command, string $cwd = null, array $env = null, $input = null, ?float $timeout = 60
    ): Process {
        return ObjectManager::getInstance()->create(Process::class, [
            'command' => $command,
            'cwd' => $cwd,
            'env' => $env,
            'input' => $input,
            'timeout' => $timeout,
        ]);
    }
}
