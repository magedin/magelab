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

namespace MagedIn\Lab\CommandExecutor\Magento;

use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Process;

class EceTools extends CommandExecutorAbstract
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
    protected function doExecute(array $config = [])
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = '/var/www/html/vendor/bin/ece-tools';
        $args = $this->getShiftedArgv();
        $command = array_merge($command, $args);

        Process::run($command, [
            'tty' => true,
        ]);
    }

    /**
     * @return bool
     */
    private function isComposerOne(): bool
    {
        $args = $this->getShiftedArgv();
        return in_array('--one', $args) || in_array('-1', $args);
    }
}
