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

namespace MagedIn\Lab\CommandExecutor\Environment;

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Process;

class Start extends CommandExecutorAbstract
{
    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerCompose $dockerComposeCommandBuilder
    ) {
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $options = [];
        if (!empty($config['callback'])) {
            $options['callback'] = $config['callback'];
        }
        $command = $this->dockerComposeCommandBuilder->build(['up', '-d']);
        Process::run($command, $options);
        return true;
    }
}
