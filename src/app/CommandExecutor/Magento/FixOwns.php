<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\CommandExecutor\Magento;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;

class FixOwns extends \MagedIn\Lab\CommandExecutor\CommandExecutorAbstract
{
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    /**
     * @param DockerComposeExec $dockerComposeExecCommandBuilder
     */
    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder
    ) {
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
    }

    /**
     * @param array $commands
     * @param array $config
     * @return int|mixed
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $callback = $config['callback'] ?? null;
        $subcommands = ['php', 'chown', '-R', 'www:', $this->getBasePath()];
        $rootNoTtyOptions = ['root' => true];
        $command = $this->dockerComposeExecCommandBuilder->build($subcommands, $rootNoTtyOptions);
        Process::run($command, [
            'tty' => true,
            'callback' => $callback,
        ]);

        return Command::SUCCESS;
    }

    /**
     * @return string
     */
    private function getBasePath(): string
    {
        return '/var/www/html';
    }
}
