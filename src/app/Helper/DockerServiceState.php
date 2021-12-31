<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

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
