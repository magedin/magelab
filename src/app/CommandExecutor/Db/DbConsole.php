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

namespace MagedIn\Lab\CommandExecutor\Db;

use MagedIn\Lab\CommandBuilder\DockerComposeDbExec;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Exception\RuntimeException;

class DbConsole extends CommandExecutorAbstract
{
    /**
     * @var DockerComposeDbExec
     */
    private DockerComposeDbExec $dockerComposePhpExecCommandBuilder;

    public function __construct(
        DockerComposeDbExec $dockerComposePhpExecCommandBuilder
    ) {
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
    }

    /**
     * @return mixed|void
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        if (!$this->validateVariables()) {
            throw new RuntimeException(
                'The credentials for your database instance are not set. Maybe Magento is not installed yet.'
            );
        }

        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'mysql';
        $command[] = '-u';
        $command[] = $this->getConfig('root') ? 'root' : $this->getUser();
        $command[] = "-p".$this->getPassword();
        $command[] = '-A';
        $command[] = $this->getDefaultDatabase();

        Process::run($command, [
            'tty' => true,
        ]);
    }

    /**
     * @return bool
     */
    private function validateVariables(): bool
    {
        if (!$this->getUser() || !$this->getPassword()) {
            return false;
        }
        return true;
    }

    /**
     * @return array|false|string
     */
    private function getUser()
    {
        return $this->getConfig('username') ?:  getenv( 'MYSQL_USER');
    }

    /**
     * @return array|false|string
     */
    private function getPassword()
    {
        return $this->getConfig('password') ?:  getenv( 'MYSQL_PASSWORD');
    }

    /**
     * @return array|false|string
     */
    private function getDefaultDatabase()
    {
        return $this->getConfig('database') ?: getenv( 'MYSQL_DATABASE');
    }
}
