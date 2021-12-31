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

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Model\Process;

class DockerContainerId
{
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerCompose $dockerComposeCommandBuilder
    ) {
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    /**
     * @param string $service
     * @return string|null
     */
    public function get(string $service): ?string
    {
        $command = $this->dockerComposeCommandBuilder->build();
        $command[] = 'ps';
        $command[] = '-q';
        $command[] = $service;
        // $command[] = " | awk '{print $1}";
        $process = Process::run($command, ['pty' => true]);
        return trim($process->getOutput());
    }
}
