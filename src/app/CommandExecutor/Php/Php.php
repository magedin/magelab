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

namespace MagedIn\Lab\CommandExecutor\Php;

use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Process;

class Php extends CommandExecutorAbstract
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExecCommandBuilder;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder
    ) {
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
    }

    /**
     * @return mixed|void
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'php';
        $command = array_merge($command, $commands);
        $command = array_merge($command, $this->getShiftedArgv());

        Process::run($command, [
            'tty' => true,
        ]);
    }
}
