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

abstract class QueryExecutorAbstract extends DbAbstract
{
    /**
     * @return string
     */
    abstract protected function getQuery(): string;

    /**
     * @return array
     */
    protected function getCommand(): array
    {
        $command = $this->getBaseCommand();
        $command[] = "-e";
        $command[] = $this->getQuery();
        return $command;
    }
}
