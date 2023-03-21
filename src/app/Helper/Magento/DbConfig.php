<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2023 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Helper\Magento;

use MagedIn\Lab\Helper\DockerLab\DirList;

class DbConfig
{
    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @param DirList $dirList
     */
    public function __construct(
        DirList $dirList
    ) {
        $this->dirList = $dirList;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $config = [];
        if (file_exists($this->getMagentoEnvFile())) {
            $magentoEnv = include_once $this->getMagentoEnvFile();
            $defaultConnection = $magentoEnv['db']['connection']['default'] ?? [];

            $config['username'] = $defaultConnection['username'] ?? null;
            $config['password'] = $defaultConnection['password'] ?? null;
            $config['database'] = $defaultConnection['dbname']   ?? null;
        }
        return $config;
    }

    /**
     * @return string
     */
    private function getMagentoEnvFile(): string
    {
        $srcDir = $this->dirList->getSrcDir();
        return $srcDir . DS . implode(DS, ['app', 'etc', 'env.php']);
    }
}
