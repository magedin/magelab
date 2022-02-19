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

class DockerIp
{
    /**
     * @return string|null
     */
    public function get(): ?string
    {
        $command = ['docker', 'run', '--rm', 'alpine', 'ip', 'route'];
        $process = Process::run($command, ['pty' => true]);
        $output = trim($process->getOutput());
        $parts = explode(' ', $output);
        return $parts[2] ?? null;
    }
}
