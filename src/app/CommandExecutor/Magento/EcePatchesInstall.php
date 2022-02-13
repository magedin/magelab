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
use MagedIn\Lab\CommandExecutor\Php\Composer;
use MagedIn\Lab\Model\Process;

class EcePatchesInstall extends CommandExecutorAbstract
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExecCommandBuilder;

    private Composer $composer;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder,
        Composer $composer
    ) {
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
        $this->composer = $composer;
    }

    /**
     * @return mixed|void
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $this->composer->execute(['require', 'magento/quality-patches']);
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
