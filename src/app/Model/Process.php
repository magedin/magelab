<?php

declare(strict_types=1);

namespace MageLab\Model;

use MageLab\ObjectManager;
use Symfony\Component\Process\Process as ComponentProcess;

class Process
{
    /**
     * @param array $command
     * @param callable|null $callback
     * @param array $env
     * @param string|null $cwd
     * @param $input
     * @param float|null $timeout
     * @return ComponentProcess
     */
    public static function run(
        array $command,
        callable $callback = null,
        array $env = [],
        string $cwd = null,
        $input = null,
        ?float $timeout = 60
    ): ComponentProcess {
        /** @var ProcessFactory $processFactory */
        $processFactory = ObjectManager::getInstance()->create(ProcessFactory::class);
        $process = $processFactory->create($command, $cwd, $env, $input, $timeout);
        $process->run($callback, $env);
        return $process;
    }
}
