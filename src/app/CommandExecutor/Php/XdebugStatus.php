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

class XdebugStatus extends CommandExecutorAbstract
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExec;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExec
    ) {
        $this->dockerComposePhpExec = $dockerComposePhpExec;
    }

    /**
     * If Xdebug is enabled returns true, otherwise returns false.
     *
     * @param array $commands
     * @param array $config
     * @return bool
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $command = $this->dockerComposePhpExec->build(['php', '--version']);
        $output = Process::run($command, [
            'pty' => true,
        ]);
        preg_match("/.*Xdebug.*Copyright.*/", $output->getOutput(), $matches);
        return !empty($matches);
    }
}