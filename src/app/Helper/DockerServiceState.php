<?php

declare(strict_types=1);

namespace MagedIn\Lab\Helper;

use MagedIn\Lab\Model\Process;

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
