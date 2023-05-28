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

abstract class DbAbstract extends CommandExecutorAbstract
{
    /**
     * @var DockerComposeDbExec
     */
    protected DockerComposeDbExec $dockerComposePhpExecCommandBuilder;

    /**
     * @param DockerComposeDbExec $dockerComposePhpExecCommandBuilder
     */
    public function __construct(
        DockerComposeDbExec $dockerComposePhpExecCommandBuilder
    ) {
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
    }

    /**
     * @return void
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        if (!$this->validateVariables()) {
            throw new RuntimeException(
                'The credentials for your database instance are not set. Maybe Magento is not installed yet.'
            );
        }
        Process::run($this->getCommand(), [
            'tty' => true,
        ]);
        $this->afterExecute();
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
    }

    /**
     * @return array
     */
    abstract protected function getCommand(): array;

    /**
     * @return bool
     */
    protected function validateVariables(): bool
    {
        if (!$this->getUser() || !$this->getPassword()) {
            return false;
        }
        return true;
    }

    /**
     * @return array|false|string
     */
    protected function getUser()
    {
        return $this->getConfig('username') ?:  getenv( 'MYSQL_USER');
    }

    /**
     * @return array|false|string
     */
    protected function getPassword()
    {
        return $this->getConfig('password') ?:  getenv( 'MYSQL_PASSWORD');
    }

    /**
     * @return array|false|string
     */
    protected function getDefaultDatabase()
    {
        return $this->getConfig('database') ?: getenv( 'MYSQL_DATABASE');
    }

    /**
     * @return array
     */
    protected function getBaseCommand(): array
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'mysql';
        $command[] = '-u';
        $command[] = $this->getConfig('root') ? 'root' : $this->getUser();
        $command[] = "-p".$this->getPassword();
        return $command;
    }
}
