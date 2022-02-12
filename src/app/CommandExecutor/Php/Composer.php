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

class Composer extends CommandExecutorAbstract
{
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
        $protectedOptions = ['--one', '-1'];
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = $this->isComposerOne() ? 'composer1' : 'composer';
        $cleanOptions = array_diff($this->getShiftedArgv(), $protectedOptions);
        $command = array_merge($command, $cleanOptions);

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
