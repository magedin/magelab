<?php

declare(strict_types=1);

namespace MageLab\Helper;

use MageLab\Model\Process;

class DockerServiceState
{
    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        $process = Process::run(['docker-compose', 'ps', '-q']);
        return !empty($process->getOutput());
    }
}
