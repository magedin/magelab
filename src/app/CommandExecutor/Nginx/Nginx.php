<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\CommandExecutor\Nginx;

use MagedIn\Lab\CommandBuilder\DockerComposeNginxExec;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Process;

class Nginx extends CommandExecutorAbstract
{
    /**
     * @var DockerComposeNginxExec
     */
    private DockerComposeNginxExec $dockerComposeNginxExecCommandBuilder;

    public function __construct(
        DockerComposeNginxExec $dockerComposeNginxExecCommandBuilder
    ) {
        $this->dockerComposeNginxExecCommandBuilder = $dockerComposeNginxExecCommandBuilder;
    }

    /**
     * @return mixed|void
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $command = $this->dockerComposeNginxExecCommandBuilder->build();
        $command[] = 'nginx';
        $command = array_merge($command, $commands);
        $command = array_merge($command, $this->getShiftedArgv());

        Process::run($command, [
            'tty' => true,
        ]);
    }
}
