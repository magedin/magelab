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

class DbDrop extends DbCreate
{
    /**
     * @return string|null
     */
    protected function getQuery(): ?string
    {
        return "DROP DATABASE `{$this->getDbName()}`;";
    }
}
