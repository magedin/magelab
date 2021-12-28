<?php

declare(strict_types=1);

namespace MageLab\Helper;

use Symfony\Component\Process\Process;

class DockerServiceState
{
    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        $process = new Process(['docker-compose', 'ps', '-q']);
        $process->run();
        return !empty($process->getOutput());
    }
}
