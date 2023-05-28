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

use MagedIn\Lab\Model\Process;

class DbDump extends QueryExecutorAbstract
{
    /**
     * @var string|null
     */
    private ?string $filePath = null;

    /**
     * @return array
     */
    protected function getBaseCommand(): array
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'mysqldump';
        $command[] = '-u';
        $command[] = $this->getConfig('root') ? 'root' : $this->getUser();
        $command[] = "-p".$this->getPassword();
        $command[] = '--databases';
        $command[] = $this->getDbName();
        $command[] = '--result-file';
        $command[] = $this->getFilepath();
        return $command;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        $afterCommand = $this->dockerComposePhpExecCommandBuilder->build();
        $afterCommand[] = 'chown';
        $afterCommand[] = '1000:1000';
        $afterCommand[] = $this->getFilepath();
        Process::run($afterCommand, [
            'tty' => true,
        ]);
    }

    /**
     * @return string
     */
    private function getFilepath(): string
    {
        if (!$this->filePath) {
            $this->filePath = "/var/dumps/{$this->getFilename()}";
        }
        return $this->filePath;
    }

    /**
     * @return string
     */
    private function getFilename(): string
    {
        return "{$this->getDbName()}.{$this->getDatetime(true)}.sql";
    }

    /**
     * @param bool $includeHours
     * @return string
     */
    private function getDatetime(bool $includeHours = true): string
    {
        $format = 'Ymd';
        if ($includeHours) {
            $format = 'Ymd-H.i.s';
        }
        return date($format);
    }

    /**
     * @return string|null
     */
    protected function getQuery(): ?string
    {
        return null;
    }
}
